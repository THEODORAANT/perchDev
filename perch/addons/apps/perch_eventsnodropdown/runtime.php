<?php 	include(__DIR__.'/fieldtypes.php');

    if (!function_exists('perch_members_init')) {
        die('Please ensure that the Members app is installed and appears before the Events app in your config/apps.php file.');
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


    PerchSystem::register_search_handler('PerchEvents_SearchHandler');
    
    function perch_events_calendar($opts=false, $return=false)
    {
        $year = date('Y');
        $month = date('m');
        
        if (isset($_GET['d']) && $_GET['d']!='') {
            $date = explode('-', $_GET['d']);
            if (isset($date[0])) $year = (int)$date[0];
            if (isset($date[1])) $month = (int)$date[1];
        }
        
        $API  = new PerchAPI(1.0, 'perch_events');
        
        $Events = new PerchEvents_Events($API);

        if (isset($opts['data'])) PerchSystem::set_vars($opts['data']);
        
        $r = $Events->get_display('calendar', $month, $year, $opts);
        
    	if ($return) return $r;
    	
    	echo $r;
    }



	function perch_event_registration_form($opts=array(), $return=false)
	{
		$API  = new PerchAPI(1.0, 'perch_events');

        $defaults = [];
        $defaults['template'] = 'booking/customer_create.html';

        if (is_array($opts)) {
            $opts = array_merge($defaults, $opts);
        }else{
            $opts = $defaults;
        }


        $Template = $API->get('Template');
        $Template->set($opts['template'], 'events');
        $html = $Template->render(array());
        $html = $Template->apply_runtime_post_processing($html);

        if ($return) return $html;
        echo $html;

	}

    function perch_event_login_form($template=null, $return=false)
    {

        if (is_null($template)) {
              $template = '~perch_events/templates/booking/customer_login.html';
        }

        return perch_member_form($template, $return);
    }
    
    function perch_events_listing($opts=false, $return=false)
    {
        $year = date('Y');
        $month = date('m');
        
        if (isset($_GET['d']) && $_GET['d']!='') {
            $date = explode('-', $_GET['d']);
            if (isset($date[0])) $year = (int)$date[0];
            if (isset($date[1])) $month = (int)$date[1];
        }
        
        $API  = new PerchAPI(1.0, 'perch_events');
        
        $Events = new PerchEvents_Events($API);

        if (isset($opts['data'])) PerchSystem::set_vars($opts['data']);
        
        $r = $Events->get_display('listing', $month, $year, $opts);
        
    	if ($return) return $r;
    	
    	echo $r;
    }
    
    
    function perch_events_custom($opts=false, $return=false)
    {
        if (isset($opts['skip-template']) && $opts['skip-template']==true) {
            $return  = true; 
            $postpro = false;
        }else{
            $postpro = true;
        }

        $API  = new PerchAPI(1.0, 'perch_events');
        
        $Events = new PerchEvents_Events($API);

        if (isset($opts['data'])) PerchSystem::set_vars($opts['data']);
        
        $out = $Events->get_custom($opts);

        // Post processing - if there are still <perch:x /> tags
        if ($postpro && !is_array($out) && strpos($out, '<perch:')!==false) {
            $Template   = new PerchTemplate();
            $out        = $Template->apply_runtime_post_processing($out);
        }
        
    	if ($return) return $out;
    	
    	echo $out;
    }
    
    /**
     * 
     * Get the content of a specific field
     * @param mixed $id_or_slug the id or slug of the event
     * @param string $field the name of the field you want to return
     * @param bool $return
     */
    function perch_events_event_field($id_or_slug=0, $field="title", $return=false)
    {
        $API  = new PerchAPI(1.0, 'perch_events');
        $Events = new PerchEvents_Events($API);
        
        $r = false;
        
        if (is_numeric($id_or_slug)) {
            $eventID = intval($id_or_slug);
            $Event = $Events->find($eventID);
        }else{
            $Event = $Events->find_by_slug($id_or_slug);
        }
        
        if (is_object($Event)) {
            $r = $Event->$field();
        }
        
        if ($return) return $r;
        
        $HTML = $API->get('HTML');
        echo $HTML->encode($r);
    }

    /**
     * 
     * Gets the categories used for an event
     * @param string $id_or_slug id or slug of the current event
     * @param string $template template to render the categories
     * @param bool $return if set to true returns the output rather than echoing it
     */
    function perch_events_event_categories($id_or_slug=0, $opts='event_category_link.html', $return=false)
    {
        $id_or_slug = rtrim($id_or_slug, '/');

        $default_opts = array(
            'template'             => 'event_category_link.html',
            'skip-template'        => false,
            'cache'                => true,
        );

        if (!is_array($opts)) {
            $opts = array('template'=>$opts);
        }

        if (is_array($opts)) {
            $opts = array_merge($default_opts, $opts);
        }else{
            $opts = $default_opts;
        }

        if (isset($opts['data'])) PerchSystem::set_vars($opts['data']);

        if ($opts['skip-template']) {
            $return = true;
        }

        $cache = false;
        $template = $opts['template'];

        if ($opts['cache']) {
            
            $cache_key = 'perch_events_event_categories'.md5($id_or_slug.serialize($opts));
            $cache = PerchEvents_Cache::get_static($cache_key, 10);

            if ($opts['skip-template']) {
                $cache = unserialize($cache);
            }
            
        }

        if ($cache) {
            if ($return) return $cache;
            echo $cache; return '';
        }


        $API  = new PerchAPI(1.0, 'perch_events');
        $Events = new PerchEvents_Events($API);
        
        $eventID = false;
        
        if (is_numeric($id_or_slug)) {
            $eventID = intval($id_or_slug); 
        }else{
            $Event = $Events->find_by_slug($id_or_slug);
            if (is_object($Event)) {
                $eventID = $Event->id();
            }
        }
        
        if ($eventID!==false) {
            $Categories = new PerchEvents_Categories();
            $cats   = $Categories->get_for_event($eventID);
            
            if ($opts['skip-template']) {

                $out = array();
                foreach($cats as $Cat) {
                    $out[] = $Cat->to_array();
                }

                if ($opts['cache']) {
                    PerchEvents_Cache::save_static($cache_key, serialize($out)); 
                }

                return $out;

            }

            $Template = $API->get('Template');
            $Template->set('events/'.$template, 'events');

            $r = $Template->render_group($cats, true);

            if ($r!='') PerchBlog_Cache::save_static($cache_key, $r);            
            
            if ($return) return $r;
            echo $r;
        }
        
        return false;
    }

    /**
     * 
     * Builds an archive listing of categories. Echoes out the resulting mark-up and content
     * @param string $template
     * @param bool $return if set to true returns the output rather than echoing it
     */
    function perch_events_categories($opts=array(), $return=false)
    {
        $default_opts = array(
            'template'             => 'category_link.html',
            'skip-template'        => false,
            'cache'                => true,
            'include-empty'        => false,
            'filter'               => false,
            'past-events'          => false,
        );

        if (is_array($opts)) {
            $opts = array_merge($default_opts, $opts);
        }else{
            $opts = $default_opts;
        }

        if (isset($opts['data'])) PerchSystem::set_vars($opts['data']);

        if ($opts['skip-template']) $return = true;

        $cache = false;

        if ($opts['cache']) {
            $cache_key  = 'perch_events_categories'.md5(serialize($opts));
            $cache      = PerchEvents_Cache::get_static($cache_key, 10);
        }

        if ($cache) {
            if ($return) return $cache;
            echo $cache; return '';
        }


        $API  = new PerchAPI(1.0, 'perch_events');
        $Events = new PerchEvents_Events($API);
        
        $Categories = new PerchEvents_Categories();
        $r      = $Categories->get_custom($opts);
  
        if ($r!='' && $opts['cache']) PerchEvents_Cache::save_static($cache_key, $r);
        
        if ($return) return $r;
        echo $r;
        
        return false;
    }
    
    /**
     * Gets the title of a category from its slug
     *
     * @param string $categorySlug 
     * @param string $return 
     * @return void
     * @author Drew McLellan
     */
    function perch_events_category($id_or_slug, $opts=array(), $return=false)
    {
        $id_or_slug = rtrim($id_or_slug, '/');

        $default_opts = array(
            'template'             => 'category.html',
            'skip-template'        => false,
            'cache'                => true,
        );

        if (!is_array($opts)) {
            $opts = array('template'=>$opts);
        }

        if (is_array($opts)) {
            $opts = array_merge($default_opts, $opts);
        }else{
            $opts = $default_opts;
        }

        if (isset($opts['data'])) PerchSystem::set_vars($opts['data']);

        if ($opts['skip-template']) $return = true;

        if ($opts['cache']) {
            $cache_key = 'perch_events_category'.md5($id_or_slug);
            $cache = PerchEvents_Cache::get_static($cache_key, 10);
        }

        if ($cache) {
            if ($return) return $cache;
            echo $cache; return '';
        }
        

        $API  = new PerchAPI(1.0, 'perch_events');
        $Categories = new PerchEvents_Categories($API);

        if (is_numeric($id_or_slug)) {
            $catID = intval($id_or_slug); 
            $Category = $Categories->find_by_slug($catID);
        }else{
            $Category = $Categories->find_by_slug($id_or_slug);
        }
                
        
        if (is_object($Category)){
            $Template = $API->get('Template');
            $Template->set('events/'.$opts['template'], 'events');

            $r = $Template->render($Category);

            if ($r!='' && $opts['cache']) PerchEvents_Cache::save_static($cache_key, $r);

            if ($return) return $r;
            echo $r;
        }
        
        return false;
    }



function perch_events_form_handler($SubmittedForm)
    {

    		$API  = new PerchAPI(1.0, 'perch_events');
    		$EventsRuntime = PerchEvents_Runtime::fetch();

    		switch($SubmittedForm->formID) {

                case 'booking':
                    $EventsRuntime->add_booking($SubmittedForm);
                    break;
                case 'register':
                    $EventsRuntime->register_customer_from_form($SubmittedForm);
                    break;

    		}



        $Perch = Perch::fetch();
        $errors = $Perch->get_form_errors($SubmittedForm->formID);
        if ($errors) PerchUtil::debug($errors);
    }

    function perch_events_bookings($opts=array(), $return=false)
	{
		$opts = PerchUtil::extend([
        				'template' => 'booking/list.html',
        				'skip-template' => false,
        			], $opts);

        		if ($opts['skip-template']) $return = true;

        		if (perch_member_logged_in()) {
        			$EventsRuntime = PerchEvents_Runtime::fetch();
        			$r = $EventsRuntime->get_bookings($opts);
        		}else{
        			$r = '';
        			if ($opts['skip-template']) $r = [];
        		}

        		if ($return) return $r;
        		echo $r;
        		PerchUtil::flush_output();


	}

    include(__DIR__.'/events.php');
