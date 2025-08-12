<?php
    echo $HTML->title_panel([
        'heading' => $Lang->get('Listing all products'),
        'button'  => [
            'text' => $Lang->get('Add product'),
            'link' => $API->app_nav('perch_shop_products').'/product/edit/',
            'icon' => 'core/plus',
            'priv' => 'perch_shop.products.create',
        ],
    ], $CurrentUser);


	/* ----------------------------------------- SMART BAR ----------------------------------------- */
        include ('_products_smartbar.php');
	/* ----------------------------------------- /SMART BAR ----------------------------------------- */

    $Listing = new PerchAdminListing($CurrentUser, $HTML, $Lang, $Paging);
    $Listing->add_col([
            'title'     => 'SKU',
            'value'     => 'sku',
            'sort'      => 'sku',
            'edit_link' => 'product/edit',
            'priv'      => 'perch_shop.products.edit',
        ]);

    $Listing->add_col([
            'title'     => 'Title',
            'value'     => 'title',
            'sort'      => 'title',
        ]);    

    $Listing->add_col([
            'title'     => 'Stock',
            'value'     => 'stock_level',
            'sort'      => 'stock_level',
        ]);

    $Listing->add_col([
            'title'     => 'Price',
            'value'     => function($Item) use ($HTML) {
                return $Item->get_admin_display_prices();
            },
        ]);

    $Listing->add_delete_action([
            'priv'   => 'perch_shop.products.delete',
            'inline' => true,
            'path'   => 'product/delete',
        ]);

    echo $Listing->render($products);
