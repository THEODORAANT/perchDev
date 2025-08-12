<?php
    
    $Announcements = new PerchAnnouncements_Announcements($API);

    $Form = $API->get('Form');

    $Form->set_name('delete');

	
	$message = false;
	
	if (isset($_GET['id']) && $_GET['id']!='') {
	    $Announcement = $Announcements->find($_GET['id']);
	}else{
	    PerchUtil::redirect($API->app_path());
	}
	

    if ($Form->submitted()) {	
    	
    	if (is_object($Announcement)) {
    	    $Announcement->delete();


            if ($Form->submitted_via_ajax) {
                echo $API->app_path().'/';
                exit;
            }else{
               PerchUtil::redirect($API->app_path().'/'); 
            }


            
        }else{
            $message = $HTML->failure_message('Sorry, that Announcement could not be deleted.');
        }
    }

    
    
    $details = $Announcement->to_array();
