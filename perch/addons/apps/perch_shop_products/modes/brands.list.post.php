<?php 
    
    echo $HTML->title_panel([
        'heading' => $Lang->get('Listing all brands'),
        'button'  => [
            'text' => $Lang->get('Add brand'),
            'link' => $API->app_nav('perch_shop_products').'/brands/edit/',
            'icon' => 'core/plus',
            'priv' => 'perch_shop.brands.create',
        ],
    ], $CurrentUser); 

	/* ----------------------------------------- SMART BAR ----------------------------------------- */
       include('_brand_smartbar.php');
	/* ----------------------------------------- /SMART BAR ----------------------------------------- */

    $Listing = new PerchAdminListing($CurrentUser, $HTML, $Lang, $Paging);
    $Listing->add_col([
            'title'     => 'Title',
            'value'     => 'brandTitle',
            'sort'      => 'brandTitle',
            'edit_link' => 'edit',
            'priv'      => 'perch_shop.brands.edit',
        ]);

    $Listing->add_col([
            'title'     => 'Slug',
            'value'     => 'slug',
            'sort'      => 'slug',
        ]);


    $Listing->add_col([
            'title'     => 'Enabled',
            'value'     => 'status',
            'sort'      => 'status',
            'type'      => 'status',
        ]);
    
    $Listing->add_delete_action([
            'priv'   => 'perch_shop.brands.delete',
            'inline' => true,
            'path'   => 'delete',
        ]);

    echo $Listing->render($brands);
