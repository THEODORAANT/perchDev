<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $Announcements = new PerchAnnouncements_Announcements($API);

    $Paging = $API->get('Paging');
    $Paging->set_per_page(10);

   
    $announcements = array();

    $filter = 'future';
    
    if (isset($_GET['by']) && $_GET['by']!='') {
        $filter = $_GET['by'];
    }

    
    switch ($filter) {
        case 'past':
            $announcements = $Announcements->all($Paging, false);
            break;

        default:
            $announcements = $Announcements->all($Paging);
            
            // Install
            if ($announcements == false) {
                $Announcements->attempt_install();
            }
            
            break;
    }
