<?php
    
    echo $HTML->title_panel([
        'heading' => $Lang->get('Listing all tax locations'),
        'button'  => [
            'text' => $Lang->get('Add tax location'),
            'link' => $API->app_nav().'/tax/locations/edit/',
            'icon' => 'core/plus',
            'priv' => 'perch_shop.taxlocations.create',
        ],
    ], $CurrentUser);

	/* ----------------------------------------- SMART BAR ----------------------------------------- */
        $smartbar_selection = 'locations';
       include('_tax_smartbar.php');
	/* ----------------------------------------- /SMART BAR ----------------------------------------- */


    $Listing = new PerchAdminListing($CurrentUser, $HTML, $Lang, $Paging);
    $Listing->add_col([
            'title'     => 'Title',
            'value'     => 'locationTitle',
            'sort'      => 'locationTitle',
            'edit_link' => 'edit',
            'priv'      => 'perch_shop.taxlocations.edit',
        ]);

    $Listing->add_col([
            'title'     => 'Home location',
            'value'     => 'locationIsHome',
            'sort'      => 'locationIsHome',
            'type'      => 'status',
        ]);

    $Listing->add_col([
            'title'     => 'Default location',
            'value'     => 'locationIsDefault',
            'sort'      => 'locationIsDefault',
            'type'      => 'status',
        ]);

    $Listing->add_delete_action([
            'priv'   => 'perch_shop.taxlocations.delete',
            'inline' => true,
            'path'   => 'delete',
        ]);

    echo $Listing->render($locations);
