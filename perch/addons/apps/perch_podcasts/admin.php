<?php
	if ($CurrentUser->logged_in() && $CurrentUser->has_priv('perch_podcasts')) {
	    $this->register_app('perch_podcasts', 'Podcasts', 2, 'Podcast management', '1.3');
	    $this->require_version('perch_podcasts', '3.0');
	    $this->add_create_page('perch_podcasts', 'edit');
	}

	spl_autoload_register(function($class_name){
        if (strpos($class_name, 'PerchPodcasts_')===0) {
            include(PERCH_PATH.'/addons/apps/perch_podcasts/lib/'.$class_name.'.class.php');
            return true;
        }
        return false;
    });
