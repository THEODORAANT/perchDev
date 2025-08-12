<?php


	//include(__DIR__.'/fieldtypes.php');

    if (!function_exists('perch_members_init')) {
        die('Please ensure that the Members app is installed and appears before the Events app in your config/apps.php file.');
    }
    spl_autoload_register(function($class_name){
        if (strpos($class_name, 'PerchListings')===0) {
            include(PERCH_PATH.'/addons/apps/perch_listings/lib/'.$class_name.'.class.php');
            return true;
        }

        return false;
    });

        function perch_listings($opts=array(), $return=false)
        {
            if (!PERCH_RUNWAY) return false;

            $default_opts = array(
                'template'             => 'listing.html',
                'skip-template'        => false,
                'split-items'          => false,
                'cache'                => true,
                'include-empty'        => false,
                'filter'               => false,
            );

            if (is_array($opts)) {
                $opts = array_merge($default_opts, $opts);
            }else{
                $opts = $default_opts;
            }

            if (isset($opts['data'])) PerchSystem::set_vars($opts['data']);

            if ($opts['skip-template'] || $opts['split-items']) $return = true;

            if (isset($opts['pagination_var'])) $opts['pagination-var'] = $opts['pagination_var'];

            $cache = false;

            if ($opts['cache']) {
                $cache_key  = 'perch_listings'.md5(serialize($opts));
                $cache      = PerchListings_Cache::get_static($cache_key, 10);
            }

            if ($cache) {
                if ($return) return $cache;
                echo $cache; return '';
            }


            $API  = new PerchAPI(1.0, 'perch_listings');
            $Listings = new PerchListings_Listings($API);
            $r      = $Listings->get_custom($opts);

            if ($r!='' && $opts['cache']) PerchListings_Cache::save_static($cache_key, $r);

            if ($return) return $r;
            echo $r;

            return false;
        }

         function perch_listing_listing($id_or_slug, $return=false)
            {
                $id_or_slug = rtrim($id_or_slug, '/');

                $opts = array(

                    'template' => 'listing_view.html'
                    );

                if (is_numeric($id_or_slug)) {
                    $opts['_id'] = intval($id_or_slug);
                }else{
                    $opts['filter'] =array( 'listingSlug' => $id_or_slug);
                    $opts['match']  = 'eq';
                    $opts['value']  = $id_or_slug;
                }

                $r = perch_listing($opts, $return);
                if ($return) return $r;
                echo $r;
            }

  function perch_listing($opts=false, $return=false)
    {
        $default_opts = array(
            'skip-template'        => false,
            'split-items'          => false,
            'filter'               => false,
            'paginate'             => true,
            'template'             => false,
        );

        if (is_array($opts)) {
            $opts = array_merge($default_opts, $opts);
        }else{
            $opts = $default_opts;
        }

        if (isset($opts['data'])) PerchSystem::set_vars($opts['data']);

        if ($opts['skip-template'] || $opts['split-items']) $return = true;

        $API  = new PerchAPI(1.0, 'perch_listings');

        $Listings = new PerchListings_Listings($API);

        // tidy
        if (isset($opts['category']) && !is_array($opts['category'])) $opts['category'] = rtrim($opts['category'], '/');

        if (isset($opts['pagination_var'])) $opts['pagination-var'] = $opts['pagination_var'];

        $r = $Listings->get_custom($opts);

    	if ($return) return $r;

    	echo $r;
    }



  function perch_listings_recent_created($count=10, $return_or_opts=false, $return=false)
    {
        if (is_array($return_or_opts)) {
            $opts = $return_or_opts;
        }else{
            $return = $return_or_opts;
            $opts = array();
        }

        $default_opts = array(
                'count'      => $count,
                'template'   => 'listings_in_list.html',
                'sort'       => 'listingCreated',
                'sort-order' => 'DESC',
                'paginate'   => true,
            );

        $opts = PerchUtil::extend($default_opts, $opts);

        if (isset($opts['data'])) PerchSystem::set_vars($opts['data']);

        $r = perch_listing($opts, $return);
    	if ($return) return $r;
    	echo $r;
    }
   function perch_listings_custom($opts=false, $return=false)
    {
        if (isset($opts['skip-template']) && $opts['skip-template']==true) {
            $return  = true;
            $postpro = false;
        }else{
            $postpro = true;
        }

        $API  = new PerchAPI(1.0, 'perch_listings');

        $Listings = new PerchListings_Listings($API);

        if (isset($opts['data'])) PerchSystem::set_vars($opts['data']);

        $out = $Listings->get_custom($opts);

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
     * Gets the categories used for an event
     * @param string $id_or_slug id or slug of the current event
     * @param string $template template to render the categories
     * @param bool $return if set to true returns the output rather than echoing it
     */
    function perch_listings_listing_categories($id_or_slug=0, $opts='listing_category_link.html', $return=false)
    {
        $id_or_slug = rtrim($id_or_slug, '/');

        $default_opts = array(
            'template'             => 'listing_category_link.html',
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

            $cache_key = 'perch_listings_listing_categories'.md5($id_or_slug.serialize($opts));
            $cache = PerchListings_Cache::get_static($cache_key, 10);

            if ($opts['skip-template']) {
                $cache = unserialize($cache);
            }

        }

        if ($cache) {
            if ($return) return $cache;
            echo $cache; return '';
        }


        $API  = new PerchAPI(1.0, 'perch_listings');
        $Listings = new PerchListings_Listings($API);

        $listingID = false;

        if (is_numeric($id_or_slug)) {
            $listingID = intval($id_or_slug);
        }else{
            $Listing = $Listings->find_by_slug($id_or_slug);
            if (is_object($Listing)) {
                $listingID = $Listing->id();
            }
        }

        if ($listingID!==false) {
            $Categories = new PerchListings_Categories();
            $cats   = $Categories->get_for_listing($listingID);

            if ($opts['skip-template']) {

                $out = array();
                foreach($cats as $Cat) {
                    $out[] = $Cat->to_array();
                }

                if ($opts['cache']) {
                    PerchListings_Cache::save_static($cache_key, serialize($out));
                }

                return $out;

            }

            $Template = $API->get('Template');
            $Template->set('listings/'.$template, 'listings');

            $r = $Template->render_group($cats, true);

           // if ($r!='') PerchBlog_Cache::save_static($cache_key, $r);

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
    function perch_listings_categories($opts=array(), $return=false)
    {
        $default_opts = array(
            'template'             => 'category_link.html',
            'skip-template'        => false,
            'cache'                => false,
            'include-empty'        => false,
            'filter'               => false,

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
            $cache_key  = 'perch_listings_categories'.md5(serialize($opts));
            $cache      = PerchListings_Cache::get_static($cache_key, 10);
        }

        if ($cache) {
            if ($return) return $cache;
            echo $cache; return '';
        }


        $API  = new PerchAPI(1.0, 'perch_listings');


        $Categories = new PerchListings_Categories();
        $r      = $Categories->get_custom($opts);

        if ($r!='' && $opts['cache']) PerchListings_Cache::save_static($cache_key, $r);

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
    function perch_listings_category($id_or_slug, $opts=array(), $return=false)
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
            $cache_key = 'perch_listings_category'.md5($id_or_slug);
            $cache = PerchListings_Cache::get_static($cache_key, 10);
        }

        if ($cache) {
            if ($return) return $cache;
            echo $cache; return '';
        }


        $API  = new PerchAPI(1.0, 'perch_listings');
        $Categories = new PerchListings_Categories($API);

        if (is_numeric($id_or_slug)) {
            $catID = intval($id_or_slug);
            $Category = $Categories->find_by_slug($catID);
        }else{
            $Category = $Categories->find_by_slug($id_or_slug);
        }


        if (is_object($Category)){
            $Template = $API->get('Template');
            $Template->set('listings/'.$opts['template'], 'listings');

            $r = $Template->render($Category);

            if ($r!='' && $opts['cache']) PerchListings_Cache::save_static($cache_key, $r);

            if ($return) return $r;
            echo $r;
        }

        return false;
    }


    function perch_listings_form_handler($SubmittedForm)
        {

        		$API  = new PerchAPI(1.0, 'perch_listings');
        		$Listings_Runtime = PerchListings_Runtime::fetch();

        		switch($SubmittedForm->formID) {


                    case 'register':
                        $Listings_Runtime->register_customer_from_form($SubmittedForm);
                        break;

        		}



            $Perch = Perch::fetch();
            $errors = $Perch->get_form_errors($SubmittedForm->formID);
            if ($errors) PerchUtil::debug($errors);
        }


function perch_listings_registration_form($opts=array(), $return=false)
	{
		$API  = new PerchAPI(1.0, 'perch_listings');

        $defaults = [];
        $defaults['template'] = 'listings/customer_create.html';

        if (is_array($opts)) {
            $opts = array_merge($defaults, $opts);
        }else{
            $opts = $defaults;
        }


        $Template = $API->get('Template');
        $Template->set($opts['template'], 'listings');
        $html = $Template->render(array());
        $html = $Template->apply_runtime_post_processing($html);

        if ($return) return $html;
        echo $html;

	}

    function perch_listings_login_form($template=null, $return=false)
    {

        if (is_null($template)) {
              $template = '~perch_listings/templates/listings/customer_login.html';
        }


        return perch_member_form($template, $return);
    }
  include(__DIR__.'/events.php');
    ?>
