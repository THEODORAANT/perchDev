<?php
    $Orders     = new PerchShop_Orders($API);
    $Currencies = new PerchShop_Currencies($API);
    $Customers  = new PerchShop_Customers($API);

    $Products   = new PerchShop_Products($API);

    $customer_opts = [];
    $product_opts  = [];

    $CustomerList = $Customers->all();
    if (PerchUtil::count($CustomerList)) {
        foreach($CustomerList as $Customer) {
            $customer_opts[] = [
                'value' => $Customer->id(),
                'label' => $Customer->customerFirstName().' '.$Customer->customerLastName()
            ];
        }
    }

    $ProductList = $Products->get_for_admin_listing();
    if (PerchUtil::count($ProductList)) {
        foreach($ProductList as $Product) {
            $product_opts[] = [
                'value' => $Product->id(),
                'label' => $Product->productTitle()

            ];
        }
    }

    $edit_mode = false;
    $Order     = false;
    $shop_id   = false;
    $message   = false;
    $details   = false;

    if (PerchUtil::get('id')) {
        if (!$CurrentUser->has_priv('perch_shop.orders.edit')) {
            PerchUtil::redirect($API->app_path());
        }

        $shop_id = PerchUtil::get('id');
        $Order   = $Orders->find($shop_id);
        $edit_mode = true;
    } else {
        if (!$CurrentUser->has_priv('perch_shop.orders.create')) {
            PerchUtil::redirect($API->app_path());
        }
    }

    $Template = $API->get('Template');
    $Template->set('shop/orders/admin_order.html', 'shop');
    $tags = $Template->find_all_tags_and_repeaters();

    $Form = $API->get('Form');
    $Form->handle_empty_block_generation($Template);

    $Form->set_required_fields_from_template($Template, $details);

    if ($Form->submitted()) {
        $data = $Form->get_posted_content($Template, $Orders, $Order);


        $data['customerID'] = PerchUtil::post('customer');
        $productID = PerchUtil::post('product');
        $qty       = (int)PerchUtil::post('qty');



        if (!$Order) {
            $Currency = $Currencies->get_default();
            if ($Currency) {
                $data['currencyID'] = $Currency->id();
            }
            $Order = $Orders->create($data);
            if ($Order) {
                // ensure customer is persisted on creation
                if (isset($data['customerID'])) {
                    $Order->update(['customerID' => $data['customerID']]);
                }
                $Order->assign_invoice_number();

                if ($productID && $qty) {
                    $OrderItems = new PerchShop_OrderItems($API);
                    $Product = $Products->find($productID);
                    $price = 0;
                    if ($Product) {
                        $prod_data = $Product->to_array();
                        if (isset($prod_data['current_price'])) {
                            $price = $prod_data['current_price'];
                        }
                    }
                    $OrderItems->create([
                        'itemType'        => 'product',
                        'orderID'         => $Order->id(),
                        'productID'       => $productID,
                        'itemPrice'       => $price,
                        'itemTax'         => 0,
                        'itemTotal'       => $price * $qty,
                        'itemQty'         => $qty,
                        'itemTaxRate'     => 0,
                        'itemDiscount'    => 0,
                        'itemTaxDiscount' => 0,
                    ]);
                }


                $Order->index($Template);
                PerchUtil::redirect($Perch->get_page().'?id='.$Order->id().'&created=1');
            }
        } else {
            $Order->update($data);
            $Order->index($Template);
        }

        if (is_object($Order)) {
            $message = $HTML->success_message('Your order has been successfully edited. Return to %slisting%s', '<a href="'.$API->app_path().'">', '</a>');
        } else {
            $message = $HTML->failure_message('Sorry, that update was not successful.');
        }
    }

    if (PerchUtil::get('created') && !$message) {
        $message = $HTML->success_message('Your order has been successfully created. Return to %s listing%s', '<a href="'.$API->app_path().'">', '</a>');
    }

    if (is_object($Order)) {
        $details = $Order->to_array();
    }
