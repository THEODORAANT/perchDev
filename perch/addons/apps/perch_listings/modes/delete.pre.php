<?php
    
    $Listings = new PerchListings_Listings($API);
    //$Categories = new PerchEvents_Categories($API);

    $Form = $API->get('Form');

    $Form->set_name('delete');

	
	$message = false;
	
	if (isset($_GET['id']) && $_GET['id']!='') {
	    $Listing = $Listings->find($_GET['id']);
	}else{
	    PerchUtil::redirect($API->app_path());
	}
	

    if ($Form->submitted()) {	
    	
    	if (is_object($Listing)) {
    	    $Listing->delete();

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

    
    
    $details = $Listing->to_array();
