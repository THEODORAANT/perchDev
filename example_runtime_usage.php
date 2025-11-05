<?php
/*
 * Example usage of the Perch Announcements runtime functions.
 *
 * This script would typically live in a template or page that has already loaded
 * Perch's runtime (e.g. by including perch/runtime.php). It demonstrates how to
 * render a list of announcements using the helper functions provided by
 * perch/addons/apps/perch_announcements/runtime.php.
 */

// Ensure the Perch runtime is available. Adjust the path as required for your project.
include_once __DIR__ . '/../../../runtime.php';

// Retrieve the latest live announcements using the default template.
$announcements_html = perch_announcements_custom([
    'filter'      => 'announcementStatus',
    'match'       => 'eq',
    'value'       => 'live',
    'count'       => 5,
    'sort'        => 'announcementDateTime',
    'sort-order'  => 'DESC',
], true);

if ($announcements_html) {
    echo '<section class="announcements">';
    echo '    <h2>Latest announcements</h2>';
    echo $announcements_html;
    echo '</section>';
} else {
    echo '<p class="announcements--empty">No announcements available right now.</p>';
}

// You can also fetch a specific announcement by its slug or ID.
$single_announcement = perch_by_announcement('company-update', true);

if ($single_announcement) {
    echo '<aside class="featured-announcement">';
    echo '    <h3>Featured announcement</h3>';
    echo $single_announcement;
    echo '</aside>';
}
