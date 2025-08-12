<?php

	if (PERCH_RUNWAY_ROUTED) {
        $events_global_init = function(){
            $API  = new PerchAPI(1.0, 'perch_events');
            $API->on('page.loaded', 'perch_events_register_global_events');
        };
        $events_global_init();
    }else{
        perch_events_register_global_events();
    }


	function perch_events_register_global_events()
	{
		#PerchUtil::debug('Registering shop global events');
		$API = new PerchAPI(1.0, 'perch_events');
		
		$API->on('members.login', 'PerchEvents_Events::register_member_login');
	}
