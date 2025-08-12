<?php

	function perch_shop_order_successful()
	{
		$ShopRuntime = PerchShop_Runtime::fetch();
		$Order = $ShopRuntime->get_active_order();

		if ($Order) {
			return $Order->is_paid();
		}

		return false;
	}

	function perch_shop_successful_order_id()
	{
		$ShopRuntime = PerchShop_Runtime::fetch();
		$Order = $ShopRuntime->get_active_order();

		if ($Order) {
			return $Order->id();
		}

		return false;
	}

	function perch_shop_orders($opts=array(), $return=false)
	{
		$opts = PerchUtil::extend([
				'template' => 'shop/orders/list.html',
				'skip-template' => false,
			], $opts);

		if ($opts['skip-template']) $return = true;

		if (perch_member_logged_in()) {
			$ShopRuntime = PerchShop_Runtime::fetch();
			$r = $ShopRuntime->get_orders($opts);
		}else{
			$r = '';
			if ($opts['skip-template']) $r = [];
		}

		if ($return) return $r;
		echo $r;
		PerchUtil::flush_output();
	}

	function perch_shop_order($orderID, $opts=array(), $return=false)
	{
		$opts = PerchUtil::extend([
				'template' => 'shop/orders/order.html',
				'skip-template' => false,
				'orderID'    => $orderID,
			], $opts);


		if ($opts['skip-template']) $return = true;

		if (perch_member_logged_in()) {
			$ShopRuntime = PerchShop_Runtime::fetch();
			$r = $ShopRuntime->get_order_items($opts);
		}else{
			$r = '';
			if ($opts['skip-template']) $r = [];
		}

		if ($return) return $r;
		echo $r;
		PerchUtil::flush_output();
	}

	function perch_shop_order_items($orderID, $opts=array(), $return=false)
	{
		$opts = PerchUtil::extend([
				'template' 		=> 'shop/orders/items.html',
				'skip-template' => false,
				'orderID'		=> $orderID,
			], $opts);

		if ($opts['skip-template']) $return = true;

		if (perch_member_logged_in()) {
			$ShopRuntime = PerchShop_Runtime::fetch();
			$r = $ShopRuntime->get_order_items($opts);
		}else{
			$r = '';
			if ($opts['skip-template']) $r = [];
		}

		if ($return) return $r;
		echo $r;
		PerchUtil::flush_output();
	}