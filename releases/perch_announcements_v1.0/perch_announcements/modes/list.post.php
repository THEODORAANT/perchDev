<?php
   
    echo $HTML->title_panel([
            'heading' => $Lang->get('Listing announcements'),
            'button'  => [
                'text' => $Lang->get('Add announcement'),
                'link' => $API->app_nav().'/edit/',
                'icon' => 'core/plus',
                'priv' => 'perch_announcements.create',
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



    echo $Smartbar->render();

    if (PerchUtil::count($announcements)) {

        $Listing = new PerchAdminListing($CurrentUser, $HTML, $Lang, $Paging);
 $Listing->add_col([
            'title'     => 'Title',
            'value'     => 'announcementTitle',
            'sort'      => 'announcementTitle',
            'edit_link' => 'edit',
        ]);

        $Listing->add_col([
                'title'     => $Lang->get('Created Date'),
                'value'     => 'announcementCreatedDate',
                'sort'      => 'announcementCreatedDate',
                'format'    => ['type'=>'date', 'format'=> PERCH_DATE_SHORT.' '.PERCH_TIME_SHORT],
            ]);


        $Listing->add_delete_action([
                'priv'   => 'perch_announcements.delete',
                'inline' => true,
                'path'   => 'delete',
            ]);

        echo $Listing->render($announcements);

    } // if pages
    
