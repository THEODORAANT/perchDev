<?php
    
    $HTML = $API->get('HTML');
    $Lang = $API->get('Lang');

    $Shows = new PerchPodcasts_Shows($API); 
    $Episodes = new PerchPodcasts_Episodes($API); 

    $shows = $Shows->all();
    

?>