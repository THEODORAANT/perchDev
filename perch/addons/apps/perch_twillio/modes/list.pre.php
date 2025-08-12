<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    // Try to update
    if (file_exists('update.php')) include('update.php');
    
    $Messages = new PerchTwillio_Messages($API);

    $Paging = $API->get('Paging');
    $Paging->set_per_page(10);
    
   /* $Categories = new PerchEvents_Categories($API);
    $categories = $Categories->all();*/
   
    $messages = array();

    $filter = 'future';
    
    if (isset($_GET['by']) && $_GET['by']!='') {
        $filter = $_GET['by'];
    }

   /* if (isset($_GET['category']) && $_GET['category'] != '') {
        $filter = 'category';
        $category = $_GET['category'];
    }
    */
    
    switch ($filter) {
        case 'past':
            $messages = $Messages->all($Paging, false);
            break;
            
       /* case 'category':
            $events = $Events->get_by_category_slug($category, $Paging);
            break;*/

        default:
            $messages = $Messages->all($Paging);
            
            // Install
            if ($messages == false) {
                $Messages->attempt_install();
            }
            
            break;
    }
