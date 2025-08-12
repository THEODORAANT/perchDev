<?php
    echo $HTML->title_panel([
        'heading' => $Lang->get('Editing Files for ‘%s’', $HTML->encode($Product->title())),
        'button'  => [
            'text' => $Lang->get('Add file'),
            'link' => $API->app_nav('perch_shop_products').'/product/files/edit/?pid='.$Product->id(),
            'icon' => 'core/plus',
            'priv' => 'perch_shop.products.create',
        ],
    ], $CurrentUser);


    /* ----------------------------------------- SMART BAR ----------------------------------------- */

    $smartbar_selection = 'files';
    include('_product_smartbar.php');

    /* ---------------------------------------- /SMART BAR ----------------------------------------- */

    $Listing = new PerchAdminListing($CurrentUser, $HTML, $Lang, $Paging);
    $Listing->add_col([
            'title'     => 'Title',
            'value'     => 'fileTitle',
            'sort'      => 'fileTitle',
            'edit_link' => 'edit',
            'priv'      => 'perch_shop.products.edit',
        ]);

    $Listing->add_col([
            'title'     => 'Slug',
            'value'     => 'fileSlug',
            'sort'      => 'fileSlug',
        ]);    

    $Listing->add_col([
            'title'     => 'Path',
            'value'     => 'resourceFile',
            'sort'      => 'resourceFile',
        ]);

     $Listing->add_col([
            'title'     => 'File size',
            'value'     => function($Item) {
                return $Item->file_size();
            },
            'sort'      => 'resourceFileSize',
        ]);

    $Listing->add_delete_action([
            'priv'   => 'perch_shop.products.delete',
            'inline' => true,
            'path'   => 'delete',
        ]);

    echo $Listing->render($files);
