<?php

    // Try to update
    if (file_exists('update.php')) include('update.php');
    
    $Listings = new PerchListings_Listings($API);

    $Paging = $API->get('Paging');
    $Paging->set_per_page(10);
    
   $Categories = new PerchListings_Categories($API);
    $categories = $Categories->all();
   
    $listings = array();

    $filter = 'future';
    
    if (isset($_GET['by']) && $_GET['by']!='') {
        $filter = $_GET['by'];
    }

   if (isset($_GET['category']) && $_GET['category'] != '') {
        $filter = 'category';
        $category = $_GET['category'];
    }

    
    switch ($filter) {
        case 'past':
            $listings = $Listings->all($Paging, false);
            break;
            
        case 'category':
            $listings = $Listings->get_by_category_slug($category, $Paging);
            break;

        default:
            $listings = $Listings->all($Paging);
            
            // Install
            if ($listings == false) {
                $Listings->attempt_install();
            }
            
            break;
    }
