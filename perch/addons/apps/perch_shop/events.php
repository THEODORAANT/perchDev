<?php

	if (PERCH_RUNWAY_ROUTED) {
        $shop_global_init = function(){
            $API  = new PerchAPI(1.0, 'perch_shop');
            $API->on('page.loaded', 'perch_shop_register_global_events');
        };
        $shop_global_init();
    }else{
        perch_shop_register_global_events();
    }


	function perch_shop_register_global_events()
	{
		#PerchUtil::debug('Registering shop global events');
		$API = new PerchAPI(1.0, 'perch_shop');
		
		$API->on('shop.order_status_update', 'PerchShop_Events::order_status');
		$API->on('members.login', 'PerchShop_Events::register_member_login');
	}