<?php


    $Dispatches = new PerchTwillio_Dispatches($API);

    $Paging = $API->get('Paging');
    $Paging->set_per_page(10);
    $Messages = new PerchTwillio_Messages($API);

  /*  $categories = $Categories->all();*/

    $dispatches = array();

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
            $dispatches = $Dispatches->all($Paging, false);
            break;

       /* case 'category':
            $events = $Events->get_by_category_slug($category, $Paging);
            break;*/

        default:
            $dispatches = $Dispatches->all($Paging);

            // Install
            if ($dispatches == false) {
                $Dispatches->attempt_install();
            }

            break;
    }
