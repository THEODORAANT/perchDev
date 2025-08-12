<?php
	    $Listings = new PerchListings_Listings($API);
        $listingID = false;
        $Util = new PerchListings_Util($API);
        $Categories = new PerchListings_Categories($API);
        $categories = $Categories->all();
	$edit_mode  	= false;
	$Listing    	= false;
	$message		= false;
	$details 		= false;

	if (PerchUtil::get('id')) {

		if (!$CurrentUser->has_priv('perch_shop.products.edit')) {
		    PerchUtil::redirect($API->app_path());
		}

		$listingID = PerchUtil::get('id');
		$Listing           = $Listings->find($listingID);
		 $details  = $Listing->to_array();
		$edit_mode         = true;

	}else{
		if (!$CurrentUser->has_priv('perch_listings.listings.create')) {
		    PerchUtil::redirect($API->app_path());
		}
	}

	// Template
	$Template   = $API->get('Template');


	$Template->set('listings/listing.html', 'listings');


	$tags = $Template->find_all_tags_and_repeaters();

	$Form = $API->get('Form');
	$Form->handle_empty_block_generation($Template);

	$Form->set_required_fields_from_template($Template, $details);


	if ($Form->submitted()) {

       $data= $Form->receive( array('cat_ids'));
		$postdata		 = $Form->get_posted_content($Template, $Listings, $Listing);
//	print_r( $postdata);
		$postvars = array('listingTitle','listingDescription','listingPrice','listingSize', 'listingLocation' ,'cat_ids');




    if (isset($details['listingDynamicFields'])) {
            $prev = PerchUtil::json_safe_decode($details['listingDynamicFields'], true);
        }
    	$dynamic_fields = $Form->receive_from_template_fields($Template, $prev, $Listings, $Listing, $clear_post=true, $strip_static_fields=false);


           $data=array_merge($postdata, $data);
//print_r( $data);
 $data['listingCreated']= date('Y-m-d h:i:s');
       $data['listingDescription']=$data['listingDescHTML'];
		$search_text = $Form->get_search_text();

		if (is_object($Listing)) {
			$Listing->update($data);
			$Listing->index($Template);
		//	$Product->update_search_text($search_text);
		}else{

			$Listing = $Listings->create($data);

			if ($Listing) {
				//$Listing->index($Template);
				//$Listing->update_search_text($search_text);
				PerchUtil::redirect($Perch->get_page().'?id='.$Listing->id().'&created=1');
			}

		}

		if (is_object($Listing)) {
		    $message = $HTML->success_message('Your listing has been successfully edited. Return to %slisting listing%s', '<a href="'.$API->app_path('perch_listings') .'" class="notification-link">', '</a>');
		}else{
		    $message = $HTML->failure_message('Sorry, that update was not successful.');
		}

	}



	if (PerchUtil::get('created') && !$message) {
	    $message = $HTML->success_message('Your listing has been successfully created. Return to %slisting listing%s', '<a href="'. $API->app_path('perch_listings') .'" class="notification-link">', '</a>');
	}

	if (is_object($Listing)) {
		$details = $Listing->to_array();
	}

