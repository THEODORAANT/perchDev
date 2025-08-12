<?php
    # include the API
    include('../../../../../core/inc/api.php');
    
    $API  = new PerchAPI(1.0, 'perch_podcasts');
    $Lang = $API->get('Lang');
    $HTML = $API->get('HTML');
    $Paging = $API->get('Paging');


    # Set the page title
    $Perch->page_title = $Lang->get('Delete episode');



    # Do anything you want to do before output is started
    include('../../modes/_subnav.php');
    include('../../modes/episode.delete.pre.php');
    
    
    # Top layout
    include(PERCH_CORE . '/inc/top.php');

    
    # Display your page
    include('../../modes/episode.delete.post.php');
    
    
    # Bottom layout
    include(PERCH_CORE . '/inc/btm.php');
