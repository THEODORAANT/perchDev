<?php

    echo $HTML->title_panel([
            'heading' => $Lang->get('Listing all campaigns'),
            'form'    => [
                'action' => $Form->action(),
                'button' => $Form->submit('btnSubmit', 'Sync', 'button button-icon icon-left', true, true, PerchUI::icon('ext/o-sync', 14))
            ]
        ], $CurrentUser);

    $Alert->output();
    echo $message;

    $Smartbar = new PerchSmartbar($CurrentUser, $HTML, $Lang);

    $Smartbar->add_item([
            'active' => true,
            'title'  => 'Campaigns',
            'link'   => $API->app_nav().'/campaigns/',
            'icon'   => 'blocks/megaphone',
        ]);

    echo $Smartbar->render();


        $Listing = new PerchAdminListing($CurrentUser, $HTML, $Lang, $Paging);
    
        $Listing->add_col([
                'title'     => 'Title',
                'value'     => 'campaignTitle',
                'sort'      => 'campaignTitle',
            ]);

        $Listing->add_col([
                'title'     => 'List',
                'value'     => 'listTitle',
                'sort'      => 'listTitle',
            ]);

        $Listing->add_col([
                'title'     => 'Date',
                'value'     => 'campaignSendTime',
                'sort'      => 'campaignSendTime',
                'format'    => [
                                'type'=>'date',
                                'format'=> PERCH_DATE_LONG.' '.PERCH_TIME_SHORT
                                ]
            ]);


        echo $Listing->render($campaigns);

