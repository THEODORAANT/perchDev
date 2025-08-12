 <?php

	if (PERCH_RUNWAY_ROUTED) {
        $shop_init = function(){
            $API  = new PerchAPI(1.0, 'perch_shop');
            $API->on('page.loaded', 'perch_shop_register_runtime_events');
        };
        $shop_init();
    }else{
        perch_shop_register_runtime_events();
    }


	function perch_shop_register_runtime_events()
	{
		$API = new PerchAPI(1.0, 'perch_shop');
		
		$API->on('shop.order_create', 'PerchShop_EvidenceLogger::log_ip_address');		
		$API->on('shop.order_status_update', 'PerchShop_EvidenceLogger::log_for_order');
		

		$API->on('shop.member_update', function($Event) use ($API) {

			$PerchMembers_Auth = new PerchMembers_Auth($API);
            $PerchMembers_Auth->refresh_session_data($Event->subject);

		});

		$API->on('members.logout', function($Event) {
			$ShopRuntime = PerchShop_Runtime::fetch();
			$ShopRuntime->reset_after_logout();
		});

	}
