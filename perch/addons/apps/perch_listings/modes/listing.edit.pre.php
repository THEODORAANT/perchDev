<?php

    $Listings = new PerchListings_Listings($API);
    $listingID = false;
    $Util = new PerchListings_Util($API);
    $Categories = new PerchListings_Categories($API);
    $categories = $Categories->all();
$message = false;




    if (isset($_GET['id']) && $_GET['id']!='') {
        $listingID = (int) $_GET['id'];
        $Listing = $Listings->find($listingID);
        $details = $Listing->to_array();
    
        $heading1 = $Lang->get('Editing a Listing');
        

    }else{
        $Listing = false;
        $listingID = false;
        $details = array();

        $heading1 = $Lang->get('Adding an Listing');
    }


    $heading2 = $Lang->get('Listing details');


    $Template   = $API->get('Template');
    $Template->set('listings/listing.html', 'listings');

    $Form = $API->get('Form');

    $tags = $Template->find_all_tags_and_repeaters();

    $Form->handle_empty_block_generation($Template);

    $Form->require_field('listingTitle', 'Required');

    $Form->set_required_fields_from_template($Template);
      echo "Form submitted";
	print_r($Form->submitted());
    if ($Form->submitted()) {
    echo "submitted";
    	        

		        $postvars = array('listingTitle','listingDescription','listingPrice','listingSize', 'listingLocation' ,'cat_ids');

    	$data = $Form->receive($postvars);
    	print_r($postvars);
    	
    	$data['listingCreated'] =date("Y-m-d H:i:s") ;


        $prev = false;

        if (isset($details['listingDynamicFields'])) {
            $prev = PerchUtil::json_safe_decode($details['listingDynamicFields'], true);
        }
        
       // $dynamic_fields = $Form->receive_from_template_fields($Template, $prev, $Listings, $Listing);
    	$dynamic_fields = $Form->receive_from_template_fields($Template, $prev, $$Listings, $$Listing, $clear_post=true, $strip_static_fields=false);

    	    // fetch out static fields
                if (isset($dynamic_fields['listingDescHTML']) && is_array($dynamic_fields['listingDescHTML'])) {
                    $data['listingDescRaw']  = $dynamic_fields['listingDescHTML']['raw'];
                    $data['listingDescHTML'] = $dynamic_fields['listingDescHTML']['processed'];
                    unset($dynamic_fields['listingDescHTML']);
                }

    	$result = false;

    	  foreach($Listings->static_fields as $field) {
                    if (isset($dynamic_fields[$field])) {

                        if (is_array($dynamic_fields[$field])) {
                            if (isset($dynamic_fields[$field]['_default'])) {
                                $data[$field] = trim($dynamic_fields[$field]['_default']);
                            }

                            if (isset($dynamic_fields[$field]['processed'])) {
                                $data[$field] = trim($dynamic_fields[$field]['processed']);
                            }
                        }

                        if (!isset($data[$field])) $data[$field] = $dynamic_fields[$field];
                        unset($dynamic_fields[$field]);
                    }
                }

            	$data['listingDynamicFields'] = PerchUtil::json_safe_encode($dynamic_fields);

    	
    	
    	if (is_object($Listing)) {
    	    $result = $Listing->update($data);
    	}else{
    	    $new_listing = $Listings->create($data);
    	    if ($new_listing) {
    	        $result = true;
                $Categories->update_listing_counts();
    	        PerchUtil::redirect($API->app_path() .'/edit/?id='.$new_listing->id().'&created=1');
    	    }else{
    	        $message = $HTML->failure_message('Sorry, that message could not be updated.');
    	    }
    	}
    	
        if ($result) {
            $message = $HTML->success_message('Your message has been successfully updated. Return to %smessage listing%s', '<a href="'.$API->app_path() .'">', '</a>');
        }else{
            $message = $HTML->failure_message('Sorry, that message could not be updated.');
        }
        
        if (is_object($Listing)) {
            $details = $Listing->to_array();
        }else{
            $details = array();
        }

        $Categories->update_listing_counts();
        
    }
    
    if (isset($_GET['created']) && !$message) {
        $message = $HTML->success_message('Your message has been successfully created. Return to %smessage listing%s', '<a href="'.$API->app_path() .'">', '</a>');
    }
