<?php
   	if ($CurrentUser->logged_in() && $CurrentUser->has_priv('perch_events')) {
   		$this->register_app('perch_events', 'Events', 1, 'Events calendar', '1.9.8');
    	$this->require_version('perch_events', '3.0');
    	$this->add_setting('perch_events_detail_url', 'Event detail page path', 'text', '/events/event.php?s={eventSlug}');
	    $this->add_create_page('perch_events', 'edit');
	}


	spl_autoload_register(function($class_name){
    	if (strpos($class_name, 'PerchEvents')===0) {
    		include(PERCH_PATH.'/addons/apps/perch_events/lib/'.$class_name.'.class.php');
    		return true;
    	}
    	if (strpos($class_name, 'API_PerchEvents')>0) {
    		include(PERCH_PATH.'/addons/apps/perch_events/lib/api/'.$class_name.'.class.php');
    		return true;
    	}
    	return false;
    });
