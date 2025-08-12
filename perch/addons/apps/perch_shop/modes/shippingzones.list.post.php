<?php

    echo $HTML->title_panel([
        'heading' => $Lang->get('Listing all shipping zones'),
        'button'  => [
            'text' => $Lang->get('Add shipping zone'),
            'link' => $API->app_nav().'/shippings/zones/edit/',
            'icon' => 'core/plus',
            'priv' => 'perch_shop.shippings.create',
        ],
    ], $CurrentUser);


	/* ----------------------------------------- SMART BAR ----------------------------------------- */
       $smartbar_selection = 'zones';
       include('_shipping_smartbar.php');
	/* ----------------------------------------- /SMART BAR ----------------------------------------- */


    $Listing = new PerchAdminListing($CurrentUser, $HTML, $Lang, $Paging);
    $Listing->add_col([
            'title'     => 'Title',
            'value'     => 'zoneTitle',
            'sort'      => 'zoneTitle',
            'edit_link' => 'edit',
            'priv'      => 'perch_shop.shippings.edit',
        ]);

    $Listing->add_col([
            'title'     => 'Slug',
            'value'     => 'zoneSlug',
            'sort'      => 'zoneSlug',
        ]);

    $Listing->add_col([
            'title'     => 'Default zone',
            'value'     => 'zoneIsDefault',
            'sort'      => 'zoneIsDefault',
            'type'      => 'status',
        ]);
    
    $Listing->add_delete_action([
            'priv'   => 'perch_shop.shippings.delete',
            'inline' => true,
            'path'   => 'delete',
        ]);

    echo $Listing->render($zones);

