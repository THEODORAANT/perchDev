<?php
	$Zones = new PerchShop_ShippingZones($API);

	#PerchUtil::hold_redirects();
	
	$edit_mode = false;
	$Zone     = false;
	$shop_id = false;
	$message   = false;
	$details   = false;

	if (PerchUtil::get('id')) {

		if (!$CurrentUser->has_priv('perch_shop.shippings.edit')) {
		    PerchUtil::redirect($API->app_path());
		}

		$shop_id = PerchUtil::get('id');
		$Zone     = $Zones->find($shop_id);
		$edit_mode = true;

	}else{
		if (!$CurrentUser->has_priv('perch_shop.shippings.create')) {
		    PerchUtil::redirect($API->app_path());
		}
	}

	// Template
	$Template   = $API->get('Template');
	$Template->set('shop/shippings/zone.html', 'shop');
	$tags = $Template->find_all_tags_and_repeaters();

	$Form = $API->get('Form');
	$Form->handle_empty_block_generation($Template);

	$Form->set_required_fields_from_template($Template, $details);

	if ($Form->submitted()) {

		$data = $Form->get_posted_content($Template, $Zones, $Zone);
		
		if ($Zone) {
			$Zone->update($data);	
			$Zone->index($Template);
		}else{
			$Zone = $Zones->create($data);
			

			if ($Zone) {
				$Zone->index($Template);
				PerchUtil::redirect($Perch->get_page().'?id='.$Zone->id().'&created=1');	
			}
			
		}

		if (is_object($Zone)) {
		    $message = $HTML->success_message('Your shipping zone has been successfully edited. Return to %slisting%s', '<a href="'.$API->app_path('perch_shop') .'/shippings/zones/">', '</a>');
		}else{
		    $message = $HTML->failure_message('Sorry, that update was not successful.');
		}

	}

	if (PerchUtil::get('created') && !$message) {
	    $message = $HTML->success_message('Your shipping zone has been successfully created. Return to %s listing%s', '<a href="'. $API->app_path('perch_shop') .'/shippings/zones/">', '</a>');
	}


	if (is_object($Zone)) {
		$details = $Zone->to_array();
	}