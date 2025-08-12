<?php

    echo $HTML->title_panel([
        'heading' => $Lang->get('Listing show statistics'),
    ], $CurrentUser);


    if (PerchUtil::count($shows)) {
        echo '<script src="'.$API->app_path().'/js/sparkline.js"></script>';



    $Listing = new PerchAdminListing($CurrentUser, $HTML, $Lang, $Paging);
    $Listing->add_col([
            'title'     => $Lang->get('Show'),
            'value'     => 'showTitle',
            'sort'      => 'showTitle',
            'edit_link' => '../show',
        ]);

    $Listing->add_col([
            'title'     => $Lang->get('Trend'),
            'value'     => function($Show) {
                $s = '<canvas id="sparkshow'.$Show->id().'" width="90" height="18"></canvas>';
                    $points = $Show->report_on('play_spark'); 
                $s .= '<script>sparkline(\'sparkshow'.$Show->id().'\', ['.implode(', ', $points).'], true);</script>';
                return $s;
            },
        ]);

    $Listing->add_col([
            'title'     => $Lang->get('Episodes'),
            'value'     => 'showEpisodeCount',
            'sort'      => 'showEpisodeCount',
        ]);

    $Listing->add_col([
            'title'     => $Lang->get('Total plays'),
            'value'     => function($Show) use ($HTML) {
                return $HTML->encode($Show->report_on('total_plays')); 
            },
        ]);

    $Listing->add_col([
            'title'     => $Lang->get('Average per episode'),
            'value'     => function($Show) use ($HTML) {
                return $HTML->encode($Show->report_on('average_plays')); 
            },
        ]);
    

    echo $Listing->render($shows);

   
    foreach($shows as $Show) {
        echo $HTML->heading2($Show->showTitle());
        $episodes = $Episodes->get_by('showID', $Show->id());
        if (PerchUtil::count($episodes)) {    

            $first = true;

            $Listing = new PerchAdminListing($CurrentUser, $HTML, $Lang, $Paging);
            $Listing->add_col([
                    'title'     => $Lang->get('Episode'),
                    'value'     => 'episodeNumber',
                    'sort'      => 'episodeNumber',
                ]);

            $Listing->add_col([
                    'title'     => $Lang->get('Title'),
                    'value'     => 'episodeTitle',
                    'sort'      => 'episodeTitle',
                ]);
            
            $Listing->add_col([
                'title'     => $Lang->get('Trend'),
                'value'     => function($Episode) use ($first) {
                    $s = '';

                    $s .= '<canvas id="sparkep'.$Episode->id().'" width="90" height="18"></canvas>';
                    $points = $Episode->report_on('play_spark', array('latest'=>$first)); 
                    if ($first) {
                        $s .= '<script>sparkline(\'sparkep'.$Episode->id().'\', ['.implode(', ', $points).'], true, "#000000");</script>';
                    }else{
                        $s .= '<script>sparkline(\'sparkep'.$Episode->id().'\', ['.implode(', ', $points).'], true);</script>';    
                    }

                    $first = false;

                    return $s;
                },
            ]);

            $Listing->add_col([
                    'title'     => $Lang->get('Plays'),
                    'value'     => function($Episode) use ($HTML) {
                        return $HTML->encode($Episode->report_on('total_plays'));
                    },
                ]);


            echo $Listing->render($episodes);


        }

    }


    } // if shows
