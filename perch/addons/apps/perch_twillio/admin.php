<?php
	if ($CurrentUser->logged_in() && $CurrentUser->has_priv('perch_twillio')) {
	    $this->register_app('perch_twillio', 'Twillio ', 2, 'Manage messages', '1.0');
	   // $this->require_version('perch_members', '3.1.1');
	           $this->add_setting('perch_twillio_sid', 'TWILLIO SID', 'text', '');
	      $this->add_setting('perch_twillio_FromNumber', 'From Number ', 'text', '');

	   // $this->add_setting('perch_members_login_page', 'Login page path', 'text', '/members/login.php?r={returnURL}');
	}
	spl_autoload_register(function($class_name){
    	if (strpos($class_name, 'PerchTwillio')===0) {
    			include(PERCH_PATH.'/addons/apps/perch_twillio/lib/'.$class_name.'.class.php');
                		return true;
    	}
    	return false;
    });
