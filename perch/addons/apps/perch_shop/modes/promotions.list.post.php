<?php

    echo $HTML->title_panel([
        'heading' => $Lang->get('Listing all promotions'),
        'button'  => [
            'text' => $Lang->get('Add promotion'),
            'link' => $API->app_nav().'/promos/edit/',
            'icon' => 'core/plus',
            'priv' => 'perch_shop.promos.create',
        ],
    ], $CurrentUser);

    /* ----------------------------------------- SMART BAR ----------------------------------------- */
       include('_promo_smartbar.php');
    /* ----------------------------------------- /SMART BAR ----------------------------------------- */

    $Listing = new PerchAdminListing($CurrentUser, $HTML, $Lang, $Paging);
    $Listing->add_col([
            'title'     => 'Title',
            'value'     => 'promoTitle',
            'sort'      => 'promoTitle',
            'edit_link' => 'edit',
            'priv'      => 'perch_shop.promos.edit',
        ]);
    
    $Listing->add_delete_action([
            'priv'   => 'perch_shop.promos.delete',
            'inline' => true,
            'path'   => 'delete',
        ]);

    echo $Listing->render($promos);
