<?php
	$Orders     = new PerchShop_Orders($API);
	$OrderItems = new PerchShop_OrderItems($API);
	$Currencies = new PerchShop_Currencies($API);
	$Customers  = new PerchShop_Customers($API);
	$Countries  = new PerchShop_Countries($API);
	$Addresses  = new PerchShop_Addresses($API);
	$Statuses   = new PerchShop_OrderStatuses($API);
	
	$Form = $API->get('Form');

	$message = false;

	if (PerchUtil::get('id')) {

		if (!$CurrentUser->has_priv('perch_shop.orders.edit')) {
		    PerchUtil::redirect($API->app_path());
		}

		$shop_id = PerchUtil::get('id');
		$Order     = $Orders->find($shop_id);

		$Currency    = $Currencies->find($Order->currencyID());
		$Customer    = $Customers->find($Order->customerID());
		$BillingAdr  = $Addresses->find($Order->orderBillingAddress());
		$ShippingAdr = $Addresses->find($Order->orderShippingAddress());

		if ($Form->submitted()) {

			$data = $Form->receive(['status']);

			if ($Order) {
				$Order->set_status($data['status']);
			}
		}


		$details = $Order->to_array();

	    $items = $OrderItems->get_for_admin($shop_id);
	   

	}else{
	    PerchUtil::redirect($API->app_path());
	}


