<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    $Messages = new PerchTwillio_Messages($API);
    $message = false;
   /* $Categories = new PerchEvents_Categories($API);
    $categories = $Categories->all();*/



    if (isset($_GET['id']) && $_GET['id']!='') {
        $messageID = (int) $_GET['id'];
        $Message = $Messages->find($messageID);
        $details = $Message->to_array();
    
        $heading1 = $Lang->get('Editing a message');
        

    }else{
        $Message = false;
        $messageID = false;
        $details = array();

        $heading1 = $Lang->get('Adding an message');
    }


    $heading2 = $Lang->get('Message details');


    $Template   = $API->get('Template');
    $Template->set('messages/message.html', 'messages');

    $Form = $API->get('Form');

    $tags = $Template->find_all_tags_and_repeaters();

    $Form->handle_empty_block_generation($Template);

    $Form->require_field('messageText', 'Required');

    $Form->set_required_fields_from_template($Template);

    if ($Form->submitted()) {
    	        
        $postvars = array('messageText');
		
    	$data = $Form->receive($postvars);
    	
    	$data['messageDateTime'] =date("Y-m-d H:i:s") ;


        $prev = false;

       /* if (isset($details['eventDynamicFields'])) {
            $prev = PerchUtil::json_safe_decode($details['eventDynamicFields'], true);
        }*/
        
       // $dynamic_fields = $Form->receive_from_template_fields($Template, $prev, $Events, $Event);

    	//$data['eventDynamicFields'] = PerchUtil::json_safe_encode($dynamic_fields);
    	
    	$result = false;
    	
    	
    	if (is_object($Message)) {
    	    $result = $Message->update($data);
    	}else{
    	    $new_message = $Messages->create($data);
    	    if ($new_message) {
    	        $result = true;
               // $Categories->update_event_counts();
    	        PerchUtil::redirect($API->app_path() .'/edit/?id='.$new_message->id().'&created=1');
    	    }else{
    	        $message = $HTML->failure_message('Sorry, that message could not be updated.');
    	    }
    	}
    	
        if ($result) {
            $message = $HTML->success_message('Your message has been successfully updated. Return to %smessage listing%s', '<a href="'.$API->app_path() .'">', '</a>');
        }else{
            $message = $HTML->failure_message('Sorry, that message could not be updated.');
        }
        
        if (is_object($Message)) {
            $details = $Message->to_array();
        }else{
            $details = array();
        }

      //  $Categories->update_event_counts();
        
    }
    
    if (isset($_GET['created']) && !$message) {
        $message = $HTML->success_message('Your message has been successfully created. Return to %smessage listing%s', '<a href="'.$API->app_path() .'">', '</a>');
    }
