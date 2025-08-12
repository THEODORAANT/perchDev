<?php
    echo $HTML->title_panel([
            'heading' => $Lang->get('Listing categories'),
            'button'  => [
                'text' => $Lang->get('Add category'),
                'link' => $API->app_nav().'/categories/edit/',
                'icon' => 'core/plus',
                'priv' => 'perch_listings.categories.manage',
                ],
        ], $CurrentUser);


    $Smartbar = new PerchSmartbar($CurrentUser, $HTML, $Lang);


    $Smartbar->add_item([
        'active' => true,
        'title' => $Lang->get('Categories'),
        'link'  => $API->app_nav().'/categories/',
        'icon'  => 'core/o-connect',
    ]);

    echo $Smartbar->render();

    if (PerchUtil::count($categories)) {


        $Listing = new PerchAdminListing($CurrentUser, $HTML, $Lang, $Paging);

        $Listing->add_col([
                'title'     => $Lang->get('Category'),
                'value'     => 'categoryTitle',
                'sort'      => 'categoryTitle',
                'edit_link' => 'edit',
            ]);

        $Listing->add_col([
                'title'     => $Lang->get('Slug'),
                'value'     => 'categorySlug',
                'sort'      => 'categorySlug',
            ]);

        
        $Listing->add_delete_action([
                'priv'   => 'perch_listings.delete',
                'inline' => true,
                'path'   => 'delete',
            ]);

        echo $Listing->render($categories);
    
    } // if pages
    
