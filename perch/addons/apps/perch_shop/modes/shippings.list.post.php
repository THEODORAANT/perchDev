<?php 

    echo $HTML->title_panel([
        'heading' => $Lang->get('Listing all shipping methods'),
        'button'  => [
            'text' => $Lang->get('Add shipping method'),
            'link' => $API->app_nav().'/shippings/edit/',
            'icon' => 'core/plus',
            'priv' => 'perch_shop.shippings.create',
        ],
    ], $CurrentUser);


	/* ----------------------------------------- SMART BAR ----------------------------------------- */
       include('_shipping_smartbar.php');
	/* ----------------------------------------- /SMART BAR ----------------------------------------- */

    $Listing = new PerchAdminListing($CurrentUser, $HTML, $Lang, $Paging);
    $Listing->add_col([
            'title'     => 'Title',
            'value'     => 'shippingTitle',
            'sort'      => 'shippingTitle',
            'edit_link' => 'edit',
            'priv'      => 'perch_shop.shippings.edit',
        ]);

    $Listing->add_col([
            'title'     => 'Slug',
            'value'     => 'shippingSlug',
            'sort'      => 'shippingSlug',
        ]);

    $Listing->add_col([
            'title'     => 'Provider',
            'value'     => function($Item) use ($HTML) {
                return $HTML->encode($Item->company());
            },
        ]);

    $Listing->add_col([
            'title'     => 'Priority',
            'value'     => 'shippingOrder',
            'sort'      => 'shippingOrder',
        ]);
    
    $Listing->add_delete_action([
            'priv'   => 'perch_shop.shippings.delete',
            'inline' => true,
            'path'   => 'delete',
        ]);

    echo $Listing->render($shippings);

