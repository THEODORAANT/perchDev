<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    $Events = new PerchEvents_Events($API);
    $message = false;
    $Categories = new PerchEvents_Categories($API);
    $categories = $Categories->all();


    $TimeSlots= new PerchEvents_TimeSlots($API);
    $timeslots = $TimeSlots->find_all();

    if (isset($_GET['id']) && $_GET['id']!='') {
        $eventID = (int) $_GET['id'];    
        $Event = $Events->find($eventID);
        $details = $Event->to_array();
    
        $heading1 = $Lang->get('Editing an event');
        

    }else{
        $Event = false;
        $eventID = false;
        $details = array();

        $heading1 = $Lang->get('Adding an event');
    }


    $heading2 = $Lang->get('Event details');


    $Template   = $API->get('Template');
    $Template->set('events/event.html', 'events');

    $Form = $API->get('Form');

    $tags = $Template->find_all_tags_and_repeaters();

    $Form->handle_empty_block_generation($Template);

    $Form->require_field('eventTitle', 'Required');
    #$Form->require_field('eventDescRaw', 'Required');
    $Form->require_field('eventDateTime_day', 'Required');
    $Form->require_field('eventEndDateTime_day', 'Required');
    $Form->set_required_fields_from_template($Template);

    if ($Form->submitted()) {
    	        
        $postvars = array('eventTitle','eventDescRaw','cat_ids','slots');
		
    	$data = $Form->receive($postvars);
    	
    	$data['eventDateTime'] = $Form->get_date('eventDateTime');
    	$data['eventEndDateTime'] = $Form->get_date('eventEndDateTime');

        $prev = false;

        if (isset($details['eventDynamicFields'])) {
            $prev = PerchUtil::json_safe_decode($details['eventDynamicFields'], true);
        }
        
        $dynamic_fields = $Form->receive_from_template_fields($Template, $prev, $Events, $Event);

    	$data['eventDynamicFields'] = PerchUtil::json_safe_encode($dynamic_fields);
    	
    	$result = false;
    	
    	
    	if (is_object($Event)) {
    	    $result = $Event->update($data);
    	}else{
    	    $new_event = $Events->create($data);
    	    if ($new_event) {
    	        $result = true;
                $Categories->update_event_counts();
    	        PerchUtil::redirect($API->app_path() .'/edit/?id='.$new_event->id().'&created=1');
    	    }else{
    	        $message = $HTML->failure_message('Sorry, that event could not be updated.');
    	    }
    	}
    	
        if ($result) {
            $message = $HTML->success_message('Your event has been successfully updated. Return to %sevent listing%s', '<a href="'.$API->app_path() .'">', '</a>');  
        }else{
            $message = $HTML->failure_message('Sorry, that event could not be updated.');
        }
        
        if (is_object($Event)) {
            $details = $Event->to_array();
        }else{
            $details = array();
        }

        $Categories->update_event_counts();
        
    }
    
    if (isset($_GET['created']) && !$message) {
        $message = $HTML->success_message('Your event has been successfully created. Return to %sevent listing%s', '<a href="'.$API->app_path() .'">', '</a>'); 
    }
