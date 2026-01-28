<?php
   	if ($CurrentUser->logged_in() && $CurrentUser->has_priv('perch_announcements')) {
   		$this->register_app('perch_announcements', 'Announcements', 1, 'Announcements', '1.0');
    	$this->require_version('perch_announcements', '1.0');
	    $this->add_create_page('perch_announcements', 'edit');
	}

	spl_autoload_register(function($class_name){

    	if (strpos($class_name, 'PerchAnnouncements')===0) {
    		include(PERCH_PATH.'/addons/apps/perch_announcements/lib/'.$class_name.'.class.php');
    		return true;
    	}

    	return false;
    });
