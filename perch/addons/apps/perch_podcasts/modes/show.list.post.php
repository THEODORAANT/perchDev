<?php

    echo $HTML->title_panel([
        'heading' => $Lang->get('Listing shows'),
        'button'  => [
            'text' => $Lang->get('Add show'),
            'link' => $API->app_nav().'/edit/',
            'icon' => 'core/plus',
            'priv' => 'perch_podcasts.shows.create',
        ],
    ], $CurrentUser);


    $Smartbar = new PerchSmartbar($CurrentUser, $HTML, $Lang);

        $Smartbar->add_item([
            'active' => true,
            'title' => 'Shows',
            'link'  => '/addons/apps/perch_podcasts/',
            'icon'  => 'blocks/theater-masks',
        ]);

    echo $Smartbar->render();


    if (PerchUtil::count($shows)) {

        $Listing = new PerchAdminListing($CurrentUser, $HTML, $Lang, $Paging);
        $Listing->add_col([
                'title'     => 'Show',
                'value'     => 'showTitle',
                'sort'      => 'showTitle',
                'edit_link' => 'show',
            ]);

        $Listing->add_col([
                'title'     => 'Slug',
                'value'     => 'showSlug',
                'sort'      => 'showSlug',
            ]);

        $Listing->add_col([
                'title'     => 'Episodes',
                'value'     => 'showEpisodeCount',
                'sort'      => 'showEpisodeCount',
            ]);
        
        $Listing->add_delete_action([
                'priv'   => 'perch_podcasts.shows.delete',
                'inline' => true,
                'path'   => 'delete',
            ]);

        echo $Listing->render($shows);
     
    } // if shows
