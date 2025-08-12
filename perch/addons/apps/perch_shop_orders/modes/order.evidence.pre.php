<?php
	$Orders = new PerchShop_Orders($API);
	$Countries = new PerchShop_Countries($API);
	$TaxExhibits = new PerchShop_TaxExhibits($API);

	$message = false;

	if (PerchUtil::get('id')) {

		if (!$CurrentUser->has_priv('perch_shop.orders.edit')) {
		    PerchUtil::redirect($API->app_path());
		}

		$shop_id = PerchUtil::get('id');
		$Order     = $Orders->find($shop_id);

	    $exhibits = $TaxExhibits->get_by('orderID', $Order->id());


	}else{
	    PerchUtil::redirect($API->app_path());
	}


