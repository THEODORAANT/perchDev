<?php
    
    $Messages = new PerchTwillio_Messages($API);
    //$Categories = new PerchEvents_Categories($API);

    $Form = $API->get('Form');

    $Form->set_name('delete');

	
	$message = false;
	
	if (isset($_GET['id']) && $_GET['id']!='') {
	    $Message = $Messages->find($_GET['id']);
	}else{
	    PerchUtil::redirect($API->app_path());
	}
	

    if ($Form->submitted()) {	
    	
    	if (is_object($Message)) {
    	    $Message->delete();

           // $Categories->update_event_counts();

            if ($Form->submitted_via_ajax) {
                echo $API->app_path().'/';
                exit;
            }else{
               PerchUtil::redirect($API->app_path().'/'); 
            }


            
        }else{
            $message = $HTML->failure_message('Sorry, that Message could not be deleted.');
        }
    }

    
    
    $details = $Message->to_array();
