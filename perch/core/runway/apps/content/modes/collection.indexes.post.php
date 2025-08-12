<?php

echo $HTML->title_panel([
                            'heading' => $Lang->get('Editing Collection Indexes'),
                            'button'  => [
                                'text' => $Lang->get('Add index'),
                                'link' => '/core/apps/content/collections/indexes/edit/?collectionID='.$Collection->id(),
                                'icon' => 'core/plus',
                                'priv' => 'content.collections.indexes',
                            ]
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


$Listing = new PerchAdminListing($CurrentUser, $HTML, $Lang, $Paging);
$Listing->add_col([
                      'title'     => 'Title',
                      'value'     => 'indexTitle',
                      'sort'      => 'indexTitle',
                      'edit_link' => '../../collections/indexes/edit',
                      'priv'      => 'content.collections.indexes',
                  ]);
$Listing->add_col([
                      'title'     => 'Slug',
                      'value'     => 'indexSlug'
                  ]);
$Listing->add_col([
                      'title'     => 'Updated',
                      'value'     => function($item){
                          return PerchUI::format_date($item->collectionUpdated());
                      },
                      'sort'      => 'indexUpdated',
                  ]);
$Listing->add_delete_action([
                                'priv'   => 'content.collections.indexes',
                                'inline' => true,
                                'path'   => 'delete'
                            ]);


echo $Listing->render($indexes);
