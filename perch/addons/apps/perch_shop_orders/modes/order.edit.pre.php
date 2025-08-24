<?php
    $Orders     = new PerchShop_Orders($API);
    $Currencies = new PerchShop_Currencies($API);
    $Customers  = new PerchShop_Customers($API);

    $customer_opts = [];
    $customer_list = $Customers->all();
    if (PerchUtil::count($customer_list)) {
        foreach($customer_list as $Customer) {
            $customer_opts[] = [
                'value' => $Customer->id(),
                'label' => $Customer->customerFirstName().' '.$Customer->customerLastName().' ('.$Customer->customerEmail().')',
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

        $postvars = ['customer'];
        $more = $Form->receive($postvars);
        if (isset($more['customer']) && $more['customer'] !== '') {
            $data['customerID'] = (int)$more['customer'];

        }

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
