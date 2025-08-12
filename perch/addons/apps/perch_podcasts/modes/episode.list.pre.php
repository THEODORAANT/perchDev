<?php
    
    $HTML = $API->get('HTML');
    $Lang = $API->get('Lang');

    $Shows = new PerchPodcasts_Shows($API); 
    $Episodes = new PerchPodcasts_Episodes($API);

    $Paging = $API->get('Paging');
    $Paging->set_per_page(15);

    $Show = $Shows->find($_GET['id']);

    $episodes = $Episodes->get_by('showID', $_GET['id'], 'episodeDate DESC', $Paging);

     if (!PerchUtil::count($episodes)) {
            $Alert->set('warning', $Lang->get('There are no episodes yet.'));
        }