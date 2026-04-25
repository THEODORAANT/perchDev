<?php
/**
 * One-shot migration: clean up corrupted blog post slugs left behind by
 * the old PerchBlog_Post::update() bug (it called date($slug, $ts) on
 * the un-tokenized format string, expanding every letter in the title
 * and date format chars cascadingly — e.g. `e` → "Europe/Helsinki",
 * `l` → "monday", `a` → "pm", etc.).
 *
 * That bug is now commented out in PerchBlog_Post.class.php and
 * PerchBlog_Posts::create() does the right thing for new posts, but any
 * row written before the fix still has a garbage `postSlug`.
 *
 * This script regenerates slugs for affected rows using the same
 * `Y-m-d-{postTitle}` rule as create(), keeps the old slug in
 * `postLegacyURL` (so a future redirect can be wired in post.php), and
 * guarantees uniqueness within `p4_blog_posts`.
 *
 * Usage:
 *   php scripts/fix-blog-slugs.php                  # dry-run (default)
 *   php scripts/fix-blog-slugs.php --apply          # write changes
 *   php scripts/fix-blog-slugs.php --apply --all    # also rewrite slugs
 *                                                   # that look clean
 */

declare(strict_types=1);

$projectRoot = dirname(__DIR__);
require $projectRoot . '/perch/runtime.php';   // boots Perch + DB + autoloader

$dryRun = !in_array('--apply', $argv, true);
$rewriteAll = in_array('--all', $argv, true);

$db = PerchDB::fetch();
$table = PERCH_DB_PREFIX . 'blog_posts';

// Heuristic markers that prove a slug came out of the old buggy date()
// expansion. These strings can't appear in a urlify()'d Finnish title.
$corruptionMarkers = [
    'europe/helsinki', 'europe-helsinki',
    'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday',
    'jan-2026', 'feb-2026',           // RFC 2822 fragments from `r`
    '+0200', '+0000', '+0300',
    'utcthu', 'utcwed', 'utcmon', 'utctue', 'utcfri', 'utcsat', 'utcsun',
    'pmpm', 'ampm',
];

function looks_corrupted(string $slug, array $markers): bool
{
    $needle = strtolower($slug);
    foreach ($markers as $m) {
        if (strpos($needle, $m) !== false) return true;
    }
    // Very long slugs are also a strong signal
    return strlen($slug) > 120;
}

function build_slug(string $title, string $dateTime): string
{
    // Mirror PerchBlog_Posts::create() with the default format
    // 'Y-m-d-{postTitle}'. We don't read the setting here — we want a
    // stable, predictable migration regardless of any wonky setting.
    $ts   = strtotime($dateTime) ?: time();
    $stem = date('Y-m-d', $ts) . '-' . $title;
    return PerchUtil::urlify($stem);
}

$rows = $db->get_rows("SELECT postID, postTitle, postSlug, postDateTime, postLegacyURL FROM `$table` ORDER BY postID");
if (!is_array($rows)) {
    fwrite(STDERR, "Could not read $table\n");
    exit(1);
}

$plan = [];
$used = [];
foreach ($rows as $r) $used[$r['postSlug']] = (int)$r['postID'];

foreach ($rows as $r) {
    $oldSlug = (string)$r['postSlug'];
    $isCorrupt = looks_corrupted($oldSlug, $corruptionMarkers);
    if (!$rewriteAll && !$isCorrupt) continue;

    $base = build_slug((string)$r['postTitle'], (string)$r['postDateTime']);
    if ($base === '') {
        fwrite(STDERR, "Skipping post {$r['postID']} — empty regenerated slug.\n");
        continue;
    }
    $candidate = $base;
    $i = 2;
    while (isset($used[$candidate]) && $used[$candidate] !== (int)$r['postID']) {
        $candidate = $base . '-' . $i++;
    }
    if ($candidate === $oldSlug) continue;

    $plan[] = [
        'postID'    => (int)$r['postID'],
        'title'     => (string)$r['postTitle'],
        'oldSlug'   => $oldSlug,
        'newSlug'   => $candidate,
        'corrupt'   => $isCorrupt,
        'legacy'    => (string)($r['postLegacyURL'] ?? ''),
    ];
    unset($used[$oldSlug]);
    $used[$candidate] = (int)$r['postID'];
}

if (!$plan) {
    echo "No slugs need updating.\n";
    exit(0);
}

echo ($dryRun ? "[DRY RUN] " : "[APPLY] ") . count($plan) . " slug(s) to rewrite:\n\n";
foreach ($plan as $p) {
    echo "  #{$p['postID']}  " . substr($p['title'], 0, 60) . "\n";
    echo "    old: {$p['oldSlug']}\n";
    echo "    new: {$p['newSlug']}\n";
    if ($p['corrupt'])  echo "    (matched corruption heuristic)\n";
    echo "\n";
}

if ($dryRun) {
    echo "Re-run with --apply to write changes.\n";
    exit(0);
}

foreach ($plan as $p) {
    $update = ['postSlug' => $p['newSlug']];
    // Only set postLegacyURL if it's currently empty — don't overwrite
    // a real legacy URL someone set deliberately.
    if ($p['legacy'] === '') {
        $update['postLegacyURL'] = $p['oldSlug'];
    }
    $ok = $db->update($table, $update, 'postID', $p['postID']);
    echo ($ok ? "OK  " : "ERR ") . "#{$p['postID']} → {$p['newSlug']}\n";
}

echo "\nDone. Old slugs preserved in postLegacyURL (where empty).\n";
echo "If you need old URLs to keep working, add a fallback in blog/post.php:\n";
echo "  if (!perch_blog_post(\$slug, true)) {\n";
echo "      // look up by postLegacyURL and 301-redirect to the canonical postSlug\n";
echo "  }\n";
