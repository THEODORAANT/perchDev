<?php

	function perch_shop_payment_form($gateway, $opts=[], $return=false)
	{
		switch ($gateway) {
			case 'stripe':
				return perch_shop_stripe_payment_form($opts, $return);
				break;
	     case 'revolut':
				return perch_shop_revolut_payment_form($opts, $return);
				break;
			case 'braintree':
				return perch_shop_braintree_payment_form($opts, $return);
				break;
		}
	}
		function perch_shop_revolut_complete_payment($details, $gateway_opts=array())
    	{
    			$Gateway = PerchShop_Gateways::get('revolut');
    		return $Gateway->complete_payment( $details);
    	}
    		function perch_shop_paypal_complete_payment()
            	{
            			$Gateway = PerchShop_Gateways::get('paypal');
            		return $Gateway->complete_payment( $_GET);
            	}
            			function perch_shop_klarna_complete_payment($klarnaOrderId)
                            	{
                            			$Gateway = PerchShop_Gateways::get('klarna');
                            		return $Gateway->complete_payment( $klarnaOrderId);
                            	}
                            function	perch_shop_klarna_confirm_payment($opts=[], $return=false)	{
                               	$Gateway = PerchShop_Gateways::get('klarna');
                               	$klarnaOrderId=$_GET['klarna_order_id'];
                           return $Gateway->complete_payment( $klarnaOrderId,$opts);
               	}
	function perch_shop_revolut_payment_form($opts=[], $return=false)
	{
	$default_opts = [
    				'template'      => 'gateways/revolut_payment_form.html',
    				'skip-template' => false,
    				'cache'         => true,
    				'cache-ttl'     => 900,
    			];

    		$opts = PerchUtil::extend($default_opts, $opts);

    		if (isset($opts['template'])) {
    			$opts['template'] = 'shop/'.$opts['template'];
    		}

    		if ($opts['skip-template']==true) $return = true;

    		$API = new PerchAPI(1.0, 'perch_shop');
    		$Template = $API->get('Template', 'shop');
    		$Template->set($opts['template'], 'shop');

    		$Gateway = PerchShop_Gateways::get('revolut');
    		$config  = PerchShop_Config::get('gateways', 'revolut');
    		$key 	 = $Gateway->get_public_api_key($config);
    		$pspmode="live";
    		if($config['test_mode']){
    		$pspmode="sandbox";
    		}

    		$ShopRuntime = PerchShop_Runtime::fetch();
    		 $addresses=$ShopRuntime->get_addresses();

	        $html = $Template->render([
	            'success_url' => $opts['success_url'],
	             'cancel_url' => $opts['cancel_url'],
				'token' => $opts['token'],
				'pspmode'=>$pspmode,
				'customer_name'=>$addresses[0]->addressFirstName()." ".$addresses[0]->addressLastName(),
				'revolutid'=> $_GET['revolutid'],
				'shop_name' => 'Shop',
			]);
		$r = $Template->apply_runtime_post_processing($html);

		if ($return) return $r;
		echo $r;
	}

	function perch_shop_stripe_payment_form($opts=[], $return=false)
	{
		$default_opts = [
				'template'      => 'gateways/stripe_payment_form.html',
				'skip-template' => false,
				'cache'         => true,
				'cache-ttl'     => 900,
			];

		$opts = PerchUtil::extend($default_opts, $opts);

		if (isset($opts['template'])) {
			$opts['template'] = 'shop/'.$opts['template'];
		}

		if ($opts['skip-template']==true) $return = true;

		$API = new PerchAPI(1.0, 'perch_shop');
		$Template = $API->get('Template', 'shop');
		$Template->set($opts['template'], 'shop');

		$Gateway = PerchShop_Gateways::get('stripe');
		$config  = PerchShop_Config::get('gateways', 'stripe');
		$key 	 = $Gateway->get_public_api_key($config);

		$ShopRuntime = PerchShop_Runtime::fetch();

		$html = $Template->render([
				'amount' => floatval($ShopRuntime->get_cart_val('grand_total', [], []))*100,
				'amount_formatted' => $ShopRuntime->get_cart_val('grand_total_formatted', [], []),
				'currency' => $ShopRuntime->get_cart_val('currency_code', [], []),
				'publishable_key' => $key,
				'shop_name' => 'Shop',
			]);
		$r = $Template->apply_runtime_post_processing($html);

		if ($return) return $r;
		echo $r;
	}

	function perch_shop_braintree_payment_form($opts=[], $return=false)
	{
		$default_opts = [
				'template'      => 'gateways/braintree_payment_form.html',
				'skip-template' => false,
				'cache'         => true,
				'cache-ttl'     => 900,
			];

		$opts = PerchUtil::extend($default_opts, $opts);

		if (isset($opts['template'])) {
			$opts['template'] = 'shop/'.$opts['template'];
		}

		if ($opts['skip-template']==true) $return = true;

		$API = new PerchAPI(1.0, 'perch_shop');
		$Template = $API->get('Template', 'shop');
		$Template->set($opts['template'], 'shop');

		$Gateway = PerchShop_Gateways::get('braintree');
		$config  = PerchShop_Config::get('gateways', 'braintree');
		$key 	 = $Gateway->get_public_api_key($config);

		$ShopRuntime = PerchShop_Runtime::fetch();

		$html = $Template->render([
				'amount'           => number_format(floatval($ShopRuntime->get_cart_val('grand_total', [], [])),2, '.', ''),
				'amount_formatted' => $ShopRuntime->get_cart_val('grand_total_formatted', [], []),
				'currency'         => $ShopRuntime->get_cart_val('currency_code', [], []),
				'client_token'     => $key,
			]);
		$r = $Template->apply_runtime_post_processing($html);

		if ($return) return $r;
		echo $r;
	}
