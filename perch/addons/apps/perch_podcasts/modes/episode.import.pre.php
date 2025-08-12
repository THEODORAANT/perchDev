<?php
    
    $HTML = $API->get('HTML');

    if (!$CurrentUser->has_priv('perch_podcasts.episodes.import')) {
        PerchUtil::redirect($API->app_path());
    }

    $importing = false;
    
    $Shows = new PerchPodcasts_Shows($API); 
    $Episodes = new PerchPodcasts_Episodes($API);
    
    $Show = $Shows->find($_GET['id']);

   	$Form = $API->get('Form');
  	
    $Form->require_field('url', 'Required');

    if ($Form->submitted()) {
    	        
        $postvars = array('url');
		
    	$data = $Form->receive($postvars);

        $Episodes->import_from_remote_rss($Show->id(), $data['url']);

        $Show->update_episode_count();

        PerchUtil::redirect($API->app_path().'/show/?id='.$Show->id());
    	
    }


?>