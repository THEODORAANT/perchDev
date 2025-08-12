<?php

	if (PERCH_RUNWAY_ROUTED) {
        $events_global_init = function(){
            $API  = new PerchAPI(1.0, 'perch_listings');
            $API->on('page.loaded', 'perch_listings_register_global_events');
        };
        $events_global_init();
    }else{
        perch_listings_register_global_events();
    }


	function perch_listings_register_global_events()
	{
		#PerchUtil::debug('Registering shop global events');
		$API = new PerchAPI(1.0, 'perch_listings');
		
		$API->on('members.login', 'PerchListings_Listings::register_member_login');
	}
