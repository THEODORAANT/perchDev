<?php

echo $HTML->title_panel([
                            'heading' => $Lang->get('Editing Collection Indexes'),
                        ], $CurrentUser);




$Smartbar = new PerchSmartbar($CurrentUser, $HTML, $Lang);

// Breadcrumb
$links = [];

$links[] = [
    'title' => 'Collections',
    'link'  => '/core/apps/content/manage/collections/',
];

$links[] = [
    'title' => $Collection->collectionKey(),
    'translate' => false,
    'link'  => '/core/apps/content/manage/collections/?id='.$Collection->id(),
];

$Smartbar->add_item([
                        'active' => false,
                        'type' => 'breadcrumb',
                        'links' => $links,
                    ]);

// Options button
$Smartbar->add_item([
                        'active' => false,
                        'title'  => 'Options',
                        'link'   => '/core/apps/content/collections/options/?id='.$Collection->id(),
                        'priv'   => 'content.collections.options',
                        'icon'   => 'core/o-toggles',
                    ]);

// Indexes button
/*$Smartbar->add_item([
                        'active' => true,
                        'title'  => 'Indexes',
                        'link'   => '/core/apps/content/collections/indexes/?id='.$Collection->id(),
                        'priv'   => 'content.collections.indexes',
                        'icon'   => 'core/o-document-magnify',
                    ]);*/


// Import button
$Smartbar->add_item([
                        'active'   => false,
                        'title'    => 'Import',
                        'link'     => '/core/apps/content/collections/import/?id='.$Collection->id(),
                        'position' => 'end',
                        'icon'     => 'core/inbox-download',
                    ]);


// Reorder button
$Smartbar->add_item([
                        'active'   => false,
                        'title'    => 'Reorder',
                        'link'     => '/core/apps/content/reorder/collection/?id='.$Collection->id(),
                        'position' => 'end',
                        'icon'     => 'core/menu',
                    ]);




echo $Smartbar->render();


?>
<form method="post" action="<?php echo PerchUtil::html($Form->action()); ?>" class="form-simple">

    <h2 class="divider"><div><?php echo PerchLang::get('Index'); ?></div></h2>

    <div class="field-wrap">
        <?php echo $Form->label('indexTitle', 'Title'); ?>
        <div class="form-entry">
            <?php
            echo $Form->text('indexTitle', $Form->get($index, 'indexTitle', 'New index'), null, false, false, ' data-urlify="indexSlug" ');
            ?>
        </div>
    </div>

    <div class="field-wrap">
        <?php echo $Form->label('indexSlug', 'Slug'); ?>
        <div class="form-entry">
            <?php
            $attr = '';
            if (isset($index['indexSlug'])) {
                $attr = 'disabled';
            }

            echo $Form->text('indexSlug', $Form->get($index, 'indexSlug', ''), null, null, null, $attr);
            ?>
        </div>
    </div>

    <?php
    // Used by column_ids and sortField
    $Template = new PerchTemplate('content/'.$Collection->collectionTemplate(), 'content');
    $tags   = $Template->find_all_tags('(content|categories)');


    $opts = [];

    $seen_tags = ['current_page', 'next_url', 'prev_url', 'number_of_pages'];
    if (PerchUtil::count($tags)) {
        foreach($tags as $Tag) {
            $tag_id = $Tag->id();
            $label = $tag_id;
            if ($Tag->output()) {
                $tag_id .='['.$Tag->output().']';
                if ($Tag->label()) {
                    $label = $Tag->label() . ' ('.$Tag->output().')';
                }
            } else {
                if ($Tag->label()) $label = $Tag->label();
            }

            if (!in_array($tag_id, $seen_tags) && $Tag->id()) {
                $seen_tags[] = $tag_id;

                $opts[] = ['label'=>$label, 'value'=>$tag_id];
            }
        }
    }

    if (isset($index['indexFields'])) {
        $vals = PerchUtil::json_safe_decode($index['indexFields']);
    } else {
        $vals = [];
    }


    echo $Form->checkbox_set('index_cols', 'Index these fields', $opts, $vals, $class='', $limit=false);

    ?>

    <div class="submit-bar">
        <div class="submit-bar-actions">
            <?php echo $Form->submit('btnsubmit', 'Save', 'button'), ' ', PerchLang::get('or'), ' <a href="',PERCH_LOGINPATH . '/core/apps/content/collections/edit/?id='.PerchUtil::html($id).'', '">', PerchLang::get('Cancel'), '</a>'; ?>
        </div>
    </div>
</form>
