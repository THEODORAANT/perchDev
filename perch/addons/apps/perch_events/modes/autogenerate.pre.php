<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    $Events = new PerchEvents_Events($API);
    $message = false;
    $Categories = new PerchEvents_Categories($API);
    $categories = $Categories->all();
 $result = false;

    $TimeSlots= new PerchEvents_TimeSlots($API);
    $timeslots = $TimeSlots->find_all();


        $Event = false;
        $eventID = false;
        $details = array();

        $heading1 = $Lang->get('Adding an event');



    $heading2 = $Lang->get('Event details');





    	    $new_event = $Events->auto_create_events(2);
    	    if ($new_event) {
    	        $result = true;
                $Categories->update_event_counts();
    	        PerchUtil::redirect($API->app_path() .'/edit/?id='.$new_event->id().'&created=1');
    	    }else{
    	        $message = $HTML->failure_message('Sorry, that event could not be updated.Already exists');
    	    }


        if ($result) {
            $message = $HTML->success_message('Your event has been successfully updated. Return to %sevent listing%s', '<a href="'.$API->app_path() .'">', '</a>');
        }else{
            $message = $HTML->failure_message('Sorry, that event could not be updated.');
        }

