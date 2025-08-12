<?php

    echo $HTML->title_panel([
            'heading' => $Lang->get('Listing Dispatches'),
            'button'  => [
                'text' => $Lang->get('New Dispatch'),
                'link' => $API->app_nav().'/dispatches/edit/',
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

    if (PerchUtil::count($dispatches)) {

        $Listing = new PerchAdminListing($CurrentUser, $HTML, $Lang, $Paging);
  $Listing->add_col([
                'title'     => $Lang->get('Dispatch ID'),
                'value'     => 'dispatchID',
                'sort'      => 'dispatchID',
                'edit_link' => 'edit',
            ]);

        $Listing->add_col([
                'title'     => $Lang->get('Message ID'),
               'value'     => 'messageID',
                'value' => function($dispatch) {
                      return '<a href= '.PERCH_LOGINPATH.'/addons/apps/perch_twillio/edit/?id='.$dispatch->messageID().'">'.$dispatch->messageID().'</a>';

                },
                /* 'value' => function($messageID) use ($Messages) {
                               $Message=  $Messages->find($messageID);
                                    return $Message->messageText();
                                    },*/
                'sort'      => 'messageID',

            ]);

        $Listing->add_col([
                'title'     => $Lang->get('Status'),
                'value'     => 'status',
                'sort'      => 'status'            ]);

         $Listing->add_col([
                 'title'     => $Lang->get('Dispatch Date'),
                 'value'     => 'dispatchDateTime',
                 'sort'      => 'dispatchDateTime',
                 'format'    => ['type'=>'date', 'format'=> PERCH_DATE_SHORT.' '.PERCH_TIME_SHORT],
             ]);



        echo $Listing->render($dispatches);

    } // if pages

