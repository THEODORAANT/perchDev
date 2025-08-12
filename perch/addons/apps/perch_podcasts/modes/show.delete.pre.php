<?php
    
    $Shows = new PerchPodcasts_Shows($API);

    $HTML = $API->get('HTML');
    $Form = $API->get('Form');
	
	$message = false;

    if (!$CurrentUser->has_priv('perch_podcasts.shows.delete')) {
        PerchUtil::redirect($API->app_path());
    }
	
	if (isset($_GET['id']) && $_GET['id']!='') {
	    $Show = $Shows->find($_GET['id'], true);
	}
	
	
	if (!is_object($Show)) PerchUtil::redirect($API->app_path());
	
	
	$Form->set_name('delete');

    if ($Form->submitted()) {
    	if (is_object($Show)) {
    	    $Show->delete();
                	    
    	    if ($Form->submitted_via_ajax) {
    	        echo $API->app_path().'/';
    	        exit;
    	    }else{
    	       PerchUtil::redirect($API->app_path().'/'); 
    	    }
    	    
            
        }else{
            $message = $HTML->failure_message('Sorry, that show could not be deleted.');
        }
    }

    
    
    $details = $Show->to_array();



?>