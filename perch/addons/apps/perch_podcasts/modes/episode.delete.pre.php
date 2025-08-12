<?php
    
    $Shows = new PerchPodcasts_Shows($API);
    $Episodes = new PerchPodcasts_Episodes($API);

    $HTML = $API->get('HTML');
    $Form = $API->get('Form');
	
	$message = false;

    if (!$CurrentUser->has_priv('perch_podcasts.episodes.delete')) {
        PerchUtil::redirect($API->app_path());
    }
	
	if (isset($_GET['id']) && $_GET['id']!='') {
	    $Episode = $Episodes->find($_GET['id'], true);
	}
	
	
	if (!is_object($Episode)) PerchUtil::redirect($API->app_path());
	
	
	$Form->set_name('delete');

    if ($Form->submitted()) {
    	if (is_object($Episode)) {
            $Show = $Shows->find($Episode->showID());
    	    $Episode->delete();
            $Show->update_episode_count();
                	    
    	    if ($Form->submitted_via_ajax) {
    	        echo $API->app_path().'/show/?id='.$Show->id();
    	        exit;
    	    }else{
    	       PerchUtil::redirect($API->app_path().'/show/?id='.$Show->id()); 
    	    }
    	    
            
        }else{
            $message = $HTML->failure_message('Sorry, that episode could not be deleted.');
        }
    }

    
    
    $details = $Episode->to_array();



?>