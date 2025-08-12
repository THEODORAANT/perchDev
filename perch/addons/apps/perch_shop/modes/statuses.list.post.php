<?php 

    echo $HTML->title_panel([
        'heading' => $Lang->get('Listing all statuses'),
        'button'  => [
            'text' => $Lang->get('Add status'),
            'link' => $API->app_nav().'/statuses/edit/',
            'icon' => 'core/plus',
            'priv' => 'perch_shop.statuses.create',
        ],
    ], $CurrentUser);


	/* ----------------------------------------- SMART BAR ----------------------------------------- */
       include('_status_smartbar.php');
	/* ----------------------------------------- /SMART BAR ----------------------------------------- */

    $Listing = new PerchAdminListing($CurrentUser, $HTML, $Lang, $Paging);
    $Listing->add_col([
            'title'     => 'Status',
            'value'     => 'statusTitle',
            'sort'      => 'statusTitle',
            'edit_link' => 'edit',
            'priv'      => 'perch_shop.statuses.edit',
        ]);

    $Listing->add_col([
            'title'     => 'Key',
            'value'     => 'statusKey',
            'sort'      => 'statusKey',
        ]);

    $Listing->add_col([
            'title'     => 'Index',
            'value'     => 'statusIndex',
            'sort'      => 'statusIndex',
        ]);

    $Listing->add_col([
            'title'     => 'Enabled',
            'value'     => 'statusActive',
            'sort'      => 'statusActive',
            'type'      => 'status',
        ]);


    $Listing->add_delete_action([
            'priv'   => 'perch_shop.statuses.delete',
            'inline' => true,
            'path'   => 'delete',
        ]);

    echo $Listing->render($statuses);

