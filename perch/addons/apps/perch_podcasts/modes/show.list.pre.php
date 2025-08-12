<?php
   
    // Try to update
    if (file_exists('update.php')) include('update.php');
    
    $Shows = new PerchPodcasts_Shows($API); 

    $shows = $Shows->all();
    
    if ($shows == false) {
        $Shows->attempt_install();
    }

    if (!PerchUtil::count($shows)) {
        $Alert->set('warning', $Lang->get('There are no shows yet.'));
    }
