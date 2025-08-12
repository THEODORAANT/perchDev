<?php
	$Shippings = new PerchShop_Shippings($API);
	
	$edit_mode = false;
	$Shipping     = false;
	$shop_id = false;
	$message   = false;
	$details   = false;

	if (PerchUtil::get('id')) {

		if (!$CurrentUser->has_priv('perch_shop.shippings.edit')) {
		    PerchUtil::redirect($API->app_path());
		}

		$shop_id = PerchUtil::get('id');
		$Shipping     = $Shippings->find($shop_id);
		$edit_mode = true;

	}else{
		if (!$CurrentUser->has_priv('perch_shop.shippings.create')) {
		    PerchUtil::redirect($API->app_path());
		}
	}

	// Template
	$Template   = $API->get('Template');
	$Template->set('shop/shippings/shipping.html', 'shop');
	$tags = $Template->find_all_tags_and_repeaters();

	$Form = $API->get('Form');
	$Form->handle_empty_block_generation($Template);

	$Form->set_required_fields_from_template($Template, $details);

	if ($Form->submitted()) {

		$data = $Form->get_posted_content($Template, $Shippings, $Shipping);
		
		if ($Shipping) {
			$Shipping->update($data);	
			$Shipping->index($Template);
		}else{
			$Shipping = $Shippings->create($data);
			$Shipping->index($Template);

			if ($Shipping) {
				PerchUtil::redirect($Perch->get_page().'?id='.$Shipping->id().'&created=1');	
			}
			
		}

		if (is_object($Shipping)) {
		    $message = $HTML->success_message('Your shipping has been successfully edited. Return to %slisting%s', '<a href="'.$API->app_path('perch_shop') .'/shippings">', '</a>');
		}else{
		    $message = $HTML->failure_message('Sorry, that update was not successful.');
		}

	}

	if (PerchUtil::get('created') && !$message) {
	    $message = $HTML->success_message('Your shipping has been successfully created. Return to %s listing%s', '<a href="'. $API->app_path('perch_shop') .'/shippings">', '</a>');
	}


	if (is_object($Shipping)) {
		$details = $Shipping->to_array();
	}