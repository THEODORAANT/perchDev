<?php 

    echo $HTML->title_panel([
        'heading' => $Lang->get('Listing all orders'),
    ], $CurrentUser);


	/* ----------------------------------------- SMART BAR ----------------------------------------- */

    include('_orders_smartbar.php');
       
	/* ----------------------------------------- /SMART BAR ----------------------------------------- */


    $Listing = new PerchAdminListing($CurrentUser, $HTML, $Lang, $Paging);
    $Listing->add_col([
            'title'     => 'Order',
            'value'     => function($Item) {
                $invoice_number = $Item->orderInvoiceNumber();
                if ($invoice_number == '') {
                    return 'Order '.$Item->id();
                }
                return $invoice_number;
            },
            'sort'      => 'orderInvoiceNumber',
            'edit_link' => 'order',
            'priv'      => 'perch_shop.orders.edit',
        ]);
    
    $Listing->add_col([
            'title'     => 'Date',
            'value'     => 'orderCreated',
            'sort'      => 'orderCreated',
            'format'    => ['type'=>'date', 'format'=>PERCH_DATE_SHORT.' '.PERCH_TIME_SHORT],
        ]);
    $Listing->add_col([
            'title'     => 'Customer',
            'value'     => 'customerName',
            'sort'      => 'customerName',
        ]);
    $Listing->add_col([
            'title'     => 'Total',
            'value'     => 'orderTotal',
            'sort'      => 'orderTotal',
        ]);
    $Listing->add_col([
            'title'     => 'Status',
            'value'     => 'statusTitle',
            'sort'      => 'orderStatus',
        ]);
    

    $Listing->add_delete_action([
            'priv'   => 'perch_shop.orders.delete',
            'inline' => true,
            'path'   => 'delete',
        ]);

    echo $Listing->render($orders);

