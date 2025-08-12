<?php
    echo $HTML->title_panel([
        'heading' => $Lang->get('Listing all customers'),
        'button'  => [
            'text' => $Lang->get('Add customer'),
            'link' => $API->app_nav().'/customers/edit/',
            'icon' => 'core/plus',
            'priv' => 'perch_shop.customers.create',
        ],
    ], $CurrentUser);

	/* ----------------------------------------- SMART BAR ----------------------------------------- */
        include('_customers_smartbar.php');
	/* ----------------------------------------- /SMART BAR ----------------------------------------- */


    $Listing = new PerchAdminListing($CurrentUser, $HTML, $Lang, $Paging);
    $Listing->add_col([
            'title'     => 'First name',
            'value'     => 'customerFirstName',
            'sort'      => 'customerFirstName',
            'edit_link' => 'edit',
            'priv'      => 'perch_shop.customers.edit',
        ]);
    
    $Listing->add_col([
            'title'     => 'Last name',
            'value'     => 'customerLastName',
            'sort'      => 'customerLastName',
        ]);
    $Listing->add_col([
            'title'     => 'Email',
            'value'     => 'customerEmail',
            'sort'      => 'customerEmail',
            'gravatar'  => 'customerEmail'
        ]);


    $Listing->add_delete_action([
            'priv'   => 'perch_shop.customers.delete',
            'inline' => true,
            'path'   => 'delete',
        ]);

    echo $Listing->render($customers);