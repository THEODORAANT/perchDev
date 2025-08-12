<?php
    # include the API
    include('../../../../../core/inc/api.php');
    
    $API  = new PerchAPI(1.0, 'perch_podcasts');
    $Lang = $API->get('Lang');
    $HTML = $API->get('HTML');
    $Paging = $API->get('Paging');


    $Perch->page_title = $Lang->get('Show episodes');

    $Perch->add_javascript($API->app_path().'/soundmanager2/soundmanager2-nodebug-jsmin.js');
    $Perch->add_javascript($API->app_path().'/js/podcasts.js');
    $Perch->add_css($API->app_path().'/css/podcasts.css');


    # Do anything you want to do before output is started
    include('../../modes/_subnav.php');
    include('../../modes/episode.edit.pre.php');
    
    
    # Top layout
    include(PERCH_CORE . '/inc/top.php');

    
    # Display your page
    include('../../modes/episode.edit.post.php');
    
    
    # Bottom layout
    include(PERCH_CORE . '/inc/btm.php');
