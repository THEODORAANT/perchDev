<?php

    echo $HTML->title_panel([
        'heading' => $Lang->get('Listing all sales'),
        'button'  => [
            'text' => $Lang->get('Add sale'),
            'link' => $API->app_nav().'/promos/sales/edit/',
            'icon' => 'core/plus',
            'priv' => 'perch_shop.sales.create',
        ],
    ], $CurrentUser);

    /* ----------------------------------------- SMART BAR ----------------------------------------- */
        $smartbar_selection = 'sales';
        include('_promo_smartbar.php');
    /* ----------------------------------------- /SMART BAR ----------------------------------------- */

    $Listing = new PerchAdminListing($CurrentUser, $HTML, $Lang, $Paging);
    $Listing->add_col([
            'title'     => 'Title',
            'value'     => 'saleTitle',
            'sort'      => 'saleTitle',
            'edit_link' => 'edit',
            'priv'      => 'perch_shop.sales.edit',
        ]);
    
    $Listing->add_delete_action([
            'priv'   => 'perch_shop.sales.delete',
            'inline' => true,
            'path'   => 'delete',
        ]);

    echo $Listing->render($sales);
