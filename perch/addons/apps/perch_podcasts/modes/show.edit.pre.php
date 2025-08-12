<?php

	$Shows = new PerchPodcasts_Shows($API);

    $HTML     = $API->get('HTML');
    $Form     = $API->get('Form');
    $Text     = $API->get('Text');
    $Template = $API->get('Template');

    // Load master template
    $Template->set('podcasts/show.html', 'podcasts');
    $Form->handle_empty_block_generation($Template);

    $result = false;
    $message = false;
    
    //check to see if we have an ID on the QueryString
    if (isset($_GET['id']) && $_GET['id']!='') {
    	//If yes, this is an edit. Get the object and turn it into an array
        $showID = (int) $_GET['id'];    
        $Show = $Shows->find($showID, true);
        $details = $Show->to_array();
    
        
    }else{
    	//If no, we're adding a new one
        $Show = false;
        $showID = false;
        $details = array();
        
    }
    
    //set required fields
    $Form->require_field('showTitle', 'Required');
    $Form->set_required_fields_from_template($Template, $details);
    
    
    if ($Form->submitted()) {
        $postvars = array('showID', 'showTitle', 'showSlug');
    	$data = $Form->receive($postvars);

        // options
        $postvars = array('fileLocation', 'fileResourceBucket', 'fileDefaultPath', 'statsURL');
        $options = $Form->receive($postvars);

        if (!isset($data['showSlug'])) {
            $data['showSlug'] = PerchUtil::urlify($data['showTitle']);
        }
                
        
        // Read in dynamic fields from the template
        $previous_values = false;

        if (isset($details['showDynamicFields'])) {
            $previous_values = PerchUtil::json_safe_decode($details['showDynamicFields'], true);
        }
        
        $dynamic_fields = $Form->receive_from_template_fields($Template, $previous_values, $Shows, $Show);
        $data['showDynamicFields'] = PerchUtil::json_safe_encode($dynamic_fields);
        
    	$redirect = false;

    	//if we have a Show update it
    	if (is_object($Show)) {
    	    $Show->update($data);
            $result = true;
    	}else{
    		//otherwise create it
    	    if (isset($data['showID'])) unset($data['showID']);
    	    
            $data['showCreated'] = date('Y-m-d H:i:s');

    	    $Show = $Shows->create($data);
    	    
    	    if ($Show) {
                $redirect = true;
    	        
    	    }else{
    	        $message = $HTML->failure_message('Sorry, that show could not be updated.');
    	    }
    	}
    	
        // options
        $Show->set_options($options);

        $Show->update_episode_count();


        if ($redirect) {
            PerchUtil::redirect($API->app_path() .'/edit/?id='.$Show->id().'&created=1');
        }

    	
        if ($result) {
            $message = $HTML->success_message('Your show has been successfully updated. Return to %sshow listing%s', '<a href="'.$API->app_path() .'">', '</a>');  
        }else{
            $message = $HTML->failure_message('Sorry, that show could not be updated.');
        }
        
        if (is_object($Show)) {
            $details = $Show->to_array();
        }else{
            $details = array();
        }
        
    }
    
    if (isset($_GET['created']) && !$message) {
        $message = $HTML->success_message('Your show has been successfully created. Return to %sshow listing%s', '<a href="'.$API->app_path() .'">', '</a>'); 
    }


    $options = array();
    if (is_object($Show)) {
        $options = $Show->get_options();
    }
    