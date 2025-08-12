<?php
   
    echo $HTML->title_panel([
            'heading' => $Lang->get('Listing messages'),
            'button'  => [
                'text' => $Lang->get('Add message'),
                'link' => $API->app_nav().'/edit/',
                'icon' => 'core/plus',
                'priv' => 'perch_twillio.create',
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

    /*if (PerchUtil::count($categories)) {
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
    }*/

    echo $Smartbar->render();

    if (PerchUtil::count($messages)) {

        $Listing = new PerchAdminListing($CurrentUser, $HTML, $Lang, $Paging);

        $Listing->add_col([
                'title'     => $Lang->get('Message'),
                'value'     => 'messageText',
                'sort'      => 'messageText',
                'edit_link' => 'edit',
            ]);

        $Listing->add_col([
                'title'     => $Lang->get('Created Date'),
                'value'     => 'messageDateTime',
                'sort'      => 'messageDateTime',
                'format'    => ['type'=>'date', 'format'=> PERCH_DATE_SHORT.' '.PERCH_TIME_SHORT],
            ]);

         $Listing->add_col([
                 'title'     => $Lang->get('Send Date'),
                 'value'     => 'sendDateTime',
                 'sort'      => 'sendDateTime',
                 'format'    => ['type'=>'date', 'format'=> PERCH_DATE_SHORT.' '.PERCH_TIME_SHORT],
             ]);

        $Listing->add_delete_action([
                'priv'   => 'perch_twillio.delete',
                'inline' => true,
                'path'   => 'delete',
            ]);

        echo $Listing->render($events);

    } // if pages
    
