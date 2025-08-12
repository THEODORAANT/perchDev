<?php

	if (PERCH_RUNWAY_ROUTED) {
        $events_global_init = function(){
            $API  = new PerchAPI(1.0, 'perch_twillio');
            $API->on('page.loaded', 'perch_twillio_register_global_events');
        };
        $events_global_init();
    }else{
        perch_twillio_register_global_events();
    }


	function perch_twillio_register_global_events()
	{
		#PerchUtil::debug('Registering shop global events');
		$API = new PerchAPI(1.0, 'perch_twillio');
		
		$API->on('members.login', 'PerchTwillio_Messages::register_member_login');
	}
