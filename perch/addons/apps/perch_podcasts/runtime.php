<?php
	spl_autoload_register(function($class_name){
        if (strpos($class_name, 'PerchPodcasts_')===0) {
            include(PERCH_PATH.'/addons/apps/perch_podcasts/lib/'.$class_name.'.class.php');
            return true;
        }
        return false;
    });


	function perch_podcasts_shows($opts=array(), $return=false)
	{
        $API  = new PerchAPI(1.0, 'perch_podcasts');   

        $defaults = array(
        	'template' => 'shows_list.html',
        	'skip-template' => false,
       	);

        if (is_array($opts)) {
            $opts = array_merge($defaults, $opts);
        }else{
            $opts = $defaults;
        }

        if (isset($opts['data'])) PerchSystem::set_vars($opts['data']);

        $Shows = new PerchPodcasts_Shows($API);
        $r = $Shows->get_custom($opts);

        if ($return) return $r;
        echo $r;
	}

	function perch_podcasts_show($showSlug, $opts=array(), $return=false)
	{
		$showSlug = rtrim($showSlug, '/');

        $API  = new PerchAPI(1.0, 'perch_podcasts');   

        $defaults = array(
        	'template' => 'show.html',
        	'skip-template' => false,
       	);

        if (is_array($opts)) {
            $opts = array_merge($defaults, $opts);
        }else{
            $opts = $defaults;
        }

        if (isset($opts['data'])) PerchSystem::set_vars($opts['data']);

        $Shows = new PerchPodcasts_Shows($API);
        $Show  = $Shows->find_by_slug($showSlug);

        if (!$opts['skip-template']) {
        	$Template = $API->get("Template");
	    	$Template->set('podcasts/'.$opts['template'], 'podcasts');
	    }

        if (is_object($Show)) {
			if ($opts['skip-template']) {
				return $Show->to_array();
			}		

	        $html = $Template->render($Show);
	        
        }else{
        	$Template->use_noresults();
	    	$html = $Template->render(array());
        }

	    if ($return) return $html;
	    echo $html;

        return false;

	}

	function perch_podcasts_episodes($showSlug, $opts=array(), $return=false)
	{
		$showSlug = rtrim($showSlug, '/');

        $API  = new PerchAPI(1.0, 'perch_podcasts');   

        $defaults = array(
        	'template' => 'episodes_list.html',
        	'skip-template' => false,
       	);

        if (is_array($opts)) {
            $opts = array_merge($defaults, $opts);
        }else{
            $opts = $defaults;
        }

        if (isset($opts['data'])) PerchSystem::set_vars($opts['data']);

        $Shows = new PerchPodcasts_Shows($API);
        $Show  = $Shows->find_by_slug($showSlug);

        if (is_object($Show)) {
        	PerchSystem::set_vars($Show->to_array());

        	$Episodes = new PerchPodcasts_Episodes($API);

        	$r = $Episodes->get_custom($Show, $opts);

	        if ($return) return $r;
	        echo $r;
        }

        return false;

	}

	function perch_podcasts_episode($showSlug, $slug_or_number, $opts=array(), $return=false)
	{
		$showSlug = rtrim($showSlug, '/');
		$slug_or_number = rtrim($slug_or_number, '/');

        $API  = new PerchAPI(1.0, 'perch_podcasts');   

        $defaults = array(
        	'template' => 'episode.html',
        	'skip-template' => false,
       	);

        if (is_array($opts)) {
            $opts = array_merge($defaults, $opts);
        }else{
            $opts = $defaults;
        }

        if (isset($opts['data'])) PerchSystem::set_vars($opts['data']);

        $Shows = new PerchPodcasts_Shows($API);
        $Show  = $Shows->find_by_slug($showSlug);

        if (is_object($Show)) {
        	PerchSystem::set_vars($Show->to_array());

        	$Episodes = new PerchPodcasts_Episodes($API);

        	if (is_numeric($slug_or_number)) {
        		$Episode = $Episodes->find_by_number($Show->id(), (int)$slug_or_number);
        	}else{
        		$Episode = $Episodes->find_by_slug($Show->id(), $slug_or_number);
        	}

        	if (!$opts['skip-template']) {
	        	$Template = $API->get("Template");
		    	$Template->set('podcasts/'.$opts['template'], 'podcasts');
		    }

	        if (is_object($Episode)) {
				if ($opts['skip-template']) {
					return $Episode->to_array();
				}		

		        $html = $Template->render($Episode);
		        
	        }else{
	        	$Template->use_noresults();
		    	$html = $Template->render(array());
	        }

		    if ($return) return $html;
		    echo $html;

	        return false;

        }

        return false;

	}

	function perch_podcasts_track_play($showSlug, $slug_or_number, $opts=array(), $return=false)
	{
		$showSlug = rtrim($showSlug, '/');
		$slug_or_number = rtrim($slug_or_number, '/');

        $API  = new PerchAPI(1.0, 'perch_podcasts');   

        $defaults = array(
        	'template' => 'episode.html',
        	'skip-template' => false,
        	'redirect' => true,
       	);

        if (is_array($opts)) {
            $opts = array_merge($defaults, $opts);
        }else{
            $opts = $defaults;
        }

        if (isset($opts['data'])) PerchSystem::set_vars($opts['data']);

        $Shows = new PerchPodcasts_Shows($API);
        $Show  = $Shows->find_by_slug($showSlug);

        if (is_object($Show)) {

        	$Episodes = new PerchPodcasts_Episodes($API);

        	if (is_numeric($slug_or_number)) {
        		$Episode = $Episodes->find_by_number($Show->id(), (int)$slug_or_number);
        	}else{
        		$Episode = $Episodes->find_by_slug($Show->id(), $slug_or_number);
        	}

	        if (is_object($Episode)) {
				
	        	$Episode->track_play($_SERVER);

				if ($opts['redirect']) {
					header('Location: '.$Episode->episodeFile(), true, 302);
					//exit;
				}
	        }
        }

        return false;

	}

