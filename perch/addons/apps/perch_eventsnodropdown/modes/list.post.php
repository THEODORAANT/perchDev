<?php
   
    echo $HTML->title_panel([
            'heading' => $Lang->get('Listing events'),
            'button'  => [
                'text' => $Lang->get('Add event'),
                'link' => $API->app_nav().'/edit/',
                'icon' => 'core/plus',
                'priv' => 'perch_events.create',
                ],
        ], $CurrentUser);
    
    if (isset($message)) echo $message;
    

    $Smartbar = new PerchSmartbar($CurrentUser, $HTML, $Lang);


    $Smartbar->add_item([
        'active' => $filter=='future',
        'title' => $Lang->get('Future'),
        'link'  => $API->app_nav().'?by=future',
    ]);

    $Smartbar->add_item([
        'active' => $filter=='past',
        'title' => $Lang->get('Past'),
        'link'  => $API->app_nav().'?by=past',
    ]);

    if (PerchUtil::count($categories)) {
        $cat_options = [];
        foreach($categories as $Category) {
            $cat_options[] = [
                    'value' => $Category->categorySlug(),
                    'title' => $Category->categoryTitle(),
                ];
        }

        $Smartbar->add_item([
            'id'      => 'cf',
            'title'   => 'By Category',
            'icon'    => 'core/o-connect',
            'active'  => PerchRequest::get('category'),
            'type'    => 'filter',
            'arg'     => 'category',
            'options' => $cat_options,
            'actions' => [

                    ],
            ]);
    }

    echo $Smartbar->render();

    if (PerchUtil::count($events)) {

        $Listing = new PerchAdminListing($CurrentUser, $HTML, $Lang, $Paging);

        $Listing->add_col([
                'title'     => $Lang->get('Event'),
                'value'     => 'eventTitle',
                'sort'      => 'eventTitle',
                'edit_link' => 'edit',
            ]);

        $Listing->add_col([
                'title'     => $Lang->get('Start Date'),
                'value'     => 'eventDateTime',
                'sort'      => 'eventDateTime',
                'format'    => ['type'=>'date', 'format'=> PERCH_DATE_SHORT.' '.PERCH_TIME_SHORT],
            ]);

         $Listing->add_col([
                 'title'     => $Lang->get('End Date'),
                 'value'     => 'eventEndDateTime',
                 'sort'      => 'eventEndDateTime',
                 'format'    => ['type'=>'date', 'format'=> PERCH_DATE_SHORT.' '.PERCH_TIME_SHORT],
             ]);

        $Listing->add_delete_action([
                'priv'   => 'perch_events.delete',
                'inline' => true,
                'path'   => 'delete',
            ]);

        echo $Listing->render($events);

    } // if pages
    
