<?php

    echo $HTML->title_panel([
        'heading' => $Lang->get('Listing all countries'),
        'button'  => [
            'text' => $Lang->get('Add country'),
            'link' => $API->app_nav().'/countries/edit/',
            'icon' => 'core/plus',
            'priv' => 'perch_shop.countries.create',
        ],
    ], $CurrentUser);

    /* ----------------------------------------- SMART BAR ----------------------------------------- */
       include('_country_smartbar.php');
    /* ----------------------------------------- /SMART BAR ----------------------------------------- */

    $Listing = new PerchAdminListing($CurrentUser, $HTML, $Lang, $Paging);
    $Listing->add_col([
            'title'     => 'Country',
            'value'     => 'country',
            'sort'      => 'country',
            'edit_link' => 'edit',
            'priv'      => 'perch_shop.countries.edit',
        ]);

    $Listing->add_col([
            'title'     => 'ISO2 code',
            'value'     => 'iso2',
            'sort'      => 'iso2',
        ]);

    $Listing->add_col([
            'title'     => 'ISO3 code',
            'value'     => 'iso3',
            'sort'      => 'iso3',
        ]);

    $Listing->add_col([
            'title'     => 'ISO number',
            'value'     => 'isonum',
            'sort'      => 'isonum',
        ]);

    $Listing->add_col([
            'title'     => 'In EU',
            'value'     => 'eu',
            'sort'      => 'eu',
            'type'      => 'status',
        ]);

    $Listing->add_col([
            'title'     => 'Enabled',
            'value'     => 'countryActive',
            'sort'      => 'countryActive',
            'type'      => 'status',
        ]);
    
    $Listing->add_delete_action([
            'priv'   => 'perch_shop.countries.delete',
            'inline' => true,
            'path'   => 'delete',
        ]);

    echo $Listing->render($countries);