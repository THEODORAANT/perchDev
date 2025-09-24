<?php
    include('../../../../core/inc/api.php');

    $API  = new PerchAPI(1.0, 'perch_blog');
    $HTML = $API->get('HTML');
    $Lang = $API->get('Lang');

    $Perch->page_title = $Lang->get('Translate Post');

    include('../modes/_subnav.php');
    include('../modes/post.translate.pre.php');

    include(PERCH_CORE . '/inc/top.php');

    include('../modes/post.translate.post.php');

    include(PERCH_CORE . '/inc/btm.php');
