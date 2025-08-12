<?php

    echo $HTML->title_panel([
        'heading' => $Lang->get('Listing all currencies'),
        'button'  => [
            'text' => $Lang->get('Add currency'),
            'link' => $API->app_nav().'/currencies/edit/',
            'icon' => 'core/plus',
            'priv' => 'perch_shop.currencies.create',
        ],
    ], $CurrentUser);

    /* ----------------------------------------- SMART BAR ----------------------------------------- */
       include('_currency_smartbar.php');
    /* ----------------------------------------- /SMART BAR ----------------------------------------- */

    $Listing = new PerchAdminListing($CurrentUser, $HTML, $Lang, $Paging);
    $Listing->add_col([
            'title'     => 'Code',
            'value'     => 'currencyCode',
            'sort'      => 'currencyCode',
            'edit_link' => 'edit',
            'priv'      => 'perch_shop.currencies.edit',
        ]);

    $Listing->add_col([
            'title'     => 'Symbol',
            'value'     => 'currencySymbol',
            'sort'      => 'currencySymbol',
        ]);

    $Listing->add_col([
            'title'     => 'Name',
            'value'     => 'currencyTitle',
            'sort'      => 'currencyTitle',
        ]);

    $Listing->add_col([
            'title'     => 'Enabled',
            'value'     => 'currencyActive',
            'sort'      => 'currencyActive',
            'type'      => 'status',
        ]);
    /*
    $Listing->add_delete_action([
            'priv'   => 'perch_shop.currencies.delete',
            'inline' => true,
            'path'   => 'delete',
        ]);
    */

    echo $Listing->render($currencies);