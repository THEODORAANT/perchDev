<?php
	if ($CurrentUser->logged_in() && $CurrentUser->has_priv('perch_listings')) {
	    $this->register_app('perch_listings', 'Listings ', 2, 'Manage listings', '1.0');
	   // $this->require_version('perch_members', '3.1.1');
	          $this->add_setting('perch_listings_price_currency', 'Price Currency', 'text', '');

	    //$this->add_setting('perch_members_login_page', 'Login page path', 'text', '/members/login.php?r={returnURL}');
	}
	spl_autoload_register(function($class_name){
    	if (strpos($class_name, 'PerchListings')===0) {
    			include(PERCH_PATH.'/addons/apps/perch_listings/lib/'.$class_name.'.class.php');
                		return true;
    	}
    	return false;
    });
