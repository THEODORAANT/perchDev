<?php

    echo $HTML->title_panel([
        'heading' => $Lang->get('Listing episodes for ‘%s’', $HTML->encode($Show->showTitle())),
        'button'  => [
            'text' => $Lang->get('Add episode'),
            'link' => $API->app_nav().'/show/episode/?show='.$Show->id(),
            'icon' => 'core/plus',
            'priv' => 'perch_podcasts.shows.create',
        ],
    ], $CurrentUser);



    $Smartbar = new PerchSmartbar($CurrentUser, $HTML, $Lang);

    $Smartbar->add_item([
        'active' => true,
        'type'  => 'breadcrumb',
        'links' => [
            [
                'title' => $Lang->get('Shows'),
                'link'  => $API->app_nav(),
            ],
            [
                'title' => $Show->showTitle(),
                'link'  => $API->app_nav().'/show/?id='.$Show->id(),
            ]
        ]
    ]);

    $Smartbar->add_item([
        'active' => false,
        'title' => $Lang->get('Options'),
        'link'  => $API->app_nav().'/edit/?id='.$Show->id(),
        'icon'  => 'core/o-toggles',
    ]);

    $Smartbar->add_item([
        'active' => false,
        'title' => $Lang->get('Import'),
        'link'  => $API->app_nav().'/show/import/?id='.$Show->id(),
        'icon'  => 'core/inbox-download',
        'position' => 'end'
    ]);


    echo $Smartbar->render();


    if (PerchUtil::count($episodes)) {


        $Listing = new PerchAdminListing($CurrentUser, $HTML, $Lang, $Paging);
        $Listing->add_col([
                'title'     => 'Episode',
                'value'     => 'episodeNumber',
                'sort'      => 'episodeNumber',
                'edit_link' => 'episode',
            ]);

        $Listing->add_col([
                'title'     => 'Title',
                'value'     => 'episodeTitle',
                'sort'      => 'episodeTitle',
            ]);

        $Listing->add_col([
                'title'     => 'Duration',
                'value'     => 'duration_hms',
            ]);

        $Listing->add_col([
                'title'     => 'Status',
                'value'     => 'episodeStatus',
                'sort'      => 'episodeStatus',
            ]);

        $Listing->add_col([
                'title'     => 'Date',
                'value'     => 'episodeDate',
                'sort'      => 'episodeDate',
                'format'    => [
                                'type'   => 'date',
                                'format' => PERCH_DATE_SHORT,
                                ]
            ]);
        
        $Listing->add_delete_action([
                'priv'   => 'perch.users.roles.delete',
                'inline' => true,
                'path'   => 'delete',
            ]);

        echo $Listing->render($episodes);

    }
