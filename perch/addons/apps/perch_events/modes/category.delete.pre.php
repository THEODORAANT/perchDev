<?php
    
    if (!$CurrentUser->has_priv('perch_events.categories.manage')) {
        exit;
    }
    
    $Categories = new PerchEvents_Categories($API);

    $Form = $API->get('Form');
    $Form->set_name('delete');
	
	$message = false;
	
	if (isset($_GET['id']) && $_GET['id']!='') {
	    $Category = $Categories->find($_GET['id']);
	}else{
	    PerchUtil::redirect($API->app_path());
	}
	

    if ($Form->submitted()) {
    	
    	if (is_object($Category)) {
    	    $Category->delete();


            if ($Form->submitted_via_ajax) {
                echo $API->app_path().'/categories/';
                exit;
            }else{
               PerchUtil::redirect($API->app_path().'/categories/');
            }

        }else{
            $message = $HTML->failure_message('Sorry, that category could not be deleted.');
        }
    }

    
    
    $details = $Category->to_array();



