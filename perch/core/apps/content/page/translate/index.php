<?php
    include(realpath(__DIR__ . '/../../../..').'/inc/pre_config.php');
    include(realpath(__DIR__ . '/../../../../..').'/config/config.php');
    include(PERCH_CORE . '/inc/loader.php');
    $Perch  = PerchAdmin::fetch();
    include(PERCH_CORE . '/inc/auth.php');
    
    
    $Perch->page_title = PerchLang::get('Translate Page');

    $app_path = PERCH_CORE.'/apps/content';
    

        
    include($app_path.'/modes/page.translate.pre.php');
    include($app_path.'/modes/_subnav.php');
    include(PERCH_CORE . '/inc/top.php');

    include($app_path.'/modes/page.translate.post.php');

    include(PERCH_CORE . '/inc/btm.php');
