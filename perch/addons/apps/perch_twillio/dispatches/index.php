<?php
    # include the API
    include('../../../../core/inc/api.php');
    
    $API  = new PerchAPI(1.0, 'perch_twillio');
    $Lang = $API->get('Lang');
    $HTML = $API->get('HTML');
    $Paging = $API->get('Paging');

    if (!$CurrentUser->has_priv('perch_twillio.dispatches.manage')) {
        PerchUtil::redirect($API->app_path());
    }
    # Set the page title
    $Perch->page_title = $Lang->get('Manage Dispatches');


    # Do anything you want to do before output is started
    include('../modes/_subnav.php');
    include('../modes/dispatches.list.pre.php');
    
    
    # Top layout
    include(PERCH_CORE . '/inc/top.php');

    
    # Display your page
    include('../modes/dispatches.list.post.php');
    
    
    # Bottom layout
    include(PERCH_CORE . '/inc/btm.php');
