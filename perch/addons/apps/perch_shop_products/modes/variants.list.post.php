<?php  echo $HTML->title_panel([
            'heading' => $Lang->get('Listing all variants'),
        ], $CurrentUser);

	/* ----------------------------------------- SMART BAR ----------------------------------------- */
    $smartbar_selection = 'variants';
    include('_product_smartbar.php');
	/* ----------------------------------------- /SMART BAR ----------------------------------------- */

    echo $Form->form_start('edit', 'inner');
    echo $message;
    echo $Form->form_end();



    $Listing = new PerchAdminListing($CurrentUser, $HTML, $Lang, $Paging);
    $Listing->add_col([
            'title'     => 'SKU',
            'value'     => 'sku',
            'sort'      => 'sku',
            'edit_link' => '../edit',
            'priv'      => 'perch_shop.products.edit',
        ]);

    $Listing->add_col([
            'title'     => 'Title',
            'value'     => 'title',
            'sort'      => 'title',
        ]);    

    $Listing->add_col([
            'title'     => 'Options',
            'value'     => 'productVariantDesc',
            'sort'      => 'productVariantDesc',
        ]);


    $Listing->add_col([
            'title'     => 'Stock',
            'value'     => 'stock_level',
            'sort'      => 'stock_level',
        ]);


    $Listing->add_delete_action([
            'priv'   => 'perch_shop.products.delete',
            'inline' => true,
            'path'   => 'delete',
        ]);

    echo $Listing->render($variants);

