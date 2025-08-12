<?php

    $Shows = new PerchPodcasts_Shows($API);
	$Episodes = new PerchPodcasts_Episodes($API);

    $HTML     = $API->get('HTML');
    $Form     = $API->get('Form');
    $Text     = $API->get('Text');
    $Template = $API->get('Template');

    // Load master template
    $Template->set('podcasts/episode.html', 'podcasts');
    $Form->handle_empty_block_generation($Template);
    

    $result = false;
    $message = false;
    
    //check to see if we have an ID on the QueryString
    if (isset($_GET['id']) && $_GET['id']!='') {
    	//If yes, this is an edit. Get the object and turn it into an array
        $episodeID = (int) $_GET['id'];    
        $Episode = $Episodes->find($episodeID);
        
        $Show = $Shows->find($Episode->showID());

        // ajax metadata update
        if (isset($_GET['dur']) || isset($_GET['bytes'])) {
            $data = array();
            if (isset($_GET['dur'])) {
                $data['episodeDuration'] = (int)$_GET['dur'];
            }
            if (isset($_GET['bytes'])) {
                $data['episodeFileSize'] = (int)$_GET['bytes'];
            }
            $Episode->update($data); 
            exit;
        }
        $details = $Episode->to_array();
        
    }else{
    	//If no, we're adding a new one
        $Episode = false;
        $episodeID = false;
        $details = array();
        $Show = $Shows->find($_GET['show']);

        $episode_number = $Show->get_next_episode_number();
        $details['episodeNumber'] = $episode_number;
        
    }

    
    //set required fields
    $Form->require_field('episodeTitle', 'Required');
    $Form->set_required_fields_from_template($Template, $details);
    
    
    if ($Form->submitted()) {
        $postvars = array('episodeID', 'episodeTitle', 'episodeSlug', 'episodeDuration', 'episodeFileSize', 'episodeFileType', 'episodeFile', 'episodeTrackedURL', 'episodeStatus', 'episodeDate');
    	$data = $Form->receive($postvars);

        $data['episodeDate'] = $Form->get_date('episodeDate');

        $templated  = array_diff($Episodes->static_fields, $postvars);

        if (PerchUtil::count($templated)) {
            foreach($templated as &$key) {
                $key = 'perch_'.$key;
            }
            $templated_data = $Form->receive($templated);

            if (PerchUtil::count($templated_data))  {
                foreach($templated_data as $key=>$val) {
                    if (!array_key_exists($key, $data) && strpos($key, 'perch_')===0) {
                        $data[str_replace('perch_', '', $key)] = $val;
                        if (isset($_POST[$key])) unset($_POST[$key]);
                    }
                }
            }
        }
                

        // Local file upload?
        if ($Show->get_option('fileLocation')=='local') {
            if ($Show->get_option('fileResourceBucket')) {
                $bucket_name = $Show->get_option('fileResourceBucket');
            }else{
                $bucket_name = 'default';
            }

            $Perch = Perch::fetch();
            $bucket = $Perch->get_resource_bucket($bucket_name);
            PerchUtil::initialise_resource_bucket($bucket);

            $targetDir = $bucket['file_path'];

            $upload_folder_writable = is_writable($targetDir);
            $filesize = 0;
            
            if (isset($_FILES['upload'])) {
                $file = $_FILES['upload']['name'];
                $filesize = $_FILES['upload']['size'];
            }
            
            // if file is greater than 0 process it into resources
            if($filesize > 0) {
                
                if($upload_folder_writable && isset($file)) {
                    $filename = PerchUtil::tidy_file_name($file);
                    if(strpos($filename,'.php')!==false) $filename.='.txt'; //checking for naughty uploading of php files.
                    $target = PerchUtil::file_path($targetDir.'/'.$filename);
                    if(file_exists($target)) {
                        $ext = strrpos($filename, '.');
                        $fileName_a = substr($filename, 0, $ext);
                        $fileName_b = substr($filename, $ext);

                        $count = 1;
                        while (file_exists(PerchUtil::file_path($targetDir.'/'.$fileName_a.'_'.$count.$fileName_b))) {
                            $count++;
                        }

                        $filename = $fileName_a . '_' . $count . $fileName_b;
                        $target = PerchUtil::file_path($targetDir.'/'.$filename);
                    }
                }
                
                PerchUtil::move_uploaded_file($_FILES['upload']['tmp_name'], $target);
                
                $data['episodeFile'] = $bucket['web_path'].'/'.$filename;           
                
            }

        }


        if (!isset($data['episodeSlug'])) {
            $data['episodeSlug'] = PerchUtil::urlify($data['episodeTitle']);
        }

        if (isset($data['episodeFile'])) {
            $file_types = array();
            $file_types['mp3']  = 'audio/mpeg';
            $file_types['m4a']  = 'audio/x-m4a';
            $file_types['mp4']  = 'video/mp4';
            $file_types['m4v']  = 'video/x-m4v';
            $file_types['mov']  = 'video/quicktime';
            $file_types['pdf']  = 'application/pdf';
            $file_types['epub'] = 'document/x-epub';

            $ext = PerchUtil::file_extension($data['episodeFile']);
            if (isset($file_types[$ext])){
                $data['episodeFileType'] = $file_types[$ext];
            }
        }


        
        if (isset($data['episodeDuration'])) {
            if (strpos($data['episodeDuration'], ':')) {
                // convert hh:mm:ss to seconds
                $str_time = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $data['episodeDuration']);
                sscanf($str_time, "%d:%d:%d", $hours, $minutes, $seconds);
                $data['episodeDuration'] = $hours * 3600 + $minutes * 60 + $seconds;
            }else{
                $data['episodeDuration'] = null;
            }
        }else{
            $data['episodeDuration'] = null;
        }


        
        // Read in dynamic fields from the template
        $previous_values = false;

        if (isset($details['episodeDynamicFields'])) {
            $previous_values = PerchUtil::json_safe_decode($details['episodeDynamicFields'], true);
        }
        
        $dynamic_fields = $Form->receive_from_template_fields($Template, $previous_values, $Episodes, $Episode);
        $data['episodeDynamicFields'] = PerchUtil::json_safe_encode($dynamic_fields);
        
    	
    	//if we have a Episode update it
    	if (is_object($Episode)) {
    	    $Episode->update($data);
            $result = true;
            $Show->update_episode_count();
    	}else{
    		//otherwise create it
    	    if (isset($data['episodeID'])) unset($data['episodeID']);
    	    $data['showID'] = $Show->id();

            

    	    $new_episode = $Episodes->create($data);

            $Show->update_episode_count();
    	    
    	    if ($new_episode) {
    	        PerchUtil::redirect($API->app_path() .'/show/episode/?id='.$new_episode->id().'&created=1');
    	    }else{
    	        $message = $HTML->failure_message('Sorry, that episode could not be updated.');
    	    }
    	}
    	
    	
        if ($result) {
            $message = $HTML->success_message('Your episode has been successfully updated. Return to %sepisode listing%s', '<a href="'.$API->app_path() .'">', '</a>');  
        }else{
            $message = $HTML->failure_message('Sorry, that episode could not be updated.');
        }
        
        if (is_object($Episode)) {
            $details = $Episode->to_array();
        }else{
            $details = array();
        }
        
    }
    
    if (isset($_GET['created']) && !$message) {
        $message = $HTML->success_message('Your episode has been successfully created. Return to %sepisode listing%s', '<a href="'.$API->app_path() .'">', '</a>'); 
    }

    $show = $Show->to_array();
