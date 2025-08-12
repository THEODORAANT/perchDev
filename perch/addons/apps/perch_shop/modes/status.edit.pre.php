<?php
	$Statuses = new PerchShop_OrderStatuses($API);
	
	$edit_mode = false;
	$Status     = false;
	$shop_id = false;
	$message   = false;
	$details   = false;

	if (PerchUtil::get('id')) {

		if (!$CurrentUser->has_priv('perch_shop.statuses.edit')) {
		    PerchUtil::redirect($API->app_path());
		}

		$shop_id = PerchUtil::get('id');
		$Status     = $Statuses->find($shop_id);
		$edit_mode = true;

	}else{
		if (!$CurrentUser->has_priv('perch_shop.statuses.create')) {
		    PerchUtil::redirect($API->app_path());
		}
	}

	// Template
	$Template   = $API->get('Template');
	$Template->set('shop/orders/status.html', 'shop');
	$tags = $Template->find_all_tags_and_repeaters();

	$Form = $API->get('Form');
	$Form->handle_empty_block_generation($Template);

	$Form->set_required_fields_from_template($Template, $details);

	if ($Form->submitted()) {

		$data = $Form->get_posted_content($Template, $Statuses, $Status);
		
		if ($Status) {
			$Status->update($data);	
			$Status->index($Template);
		}else{
			$Status = $Statuses->create($data);
			

			if ($Status) {
				$Status->index($Template);
				PerchUtil::redirect($Perch->get_page().'?id='.$Status->id().'&created=1');	
			}
			
		}

		if (is_object($Status)) {
		    $message = $HTML->success_message('Your status has been successfully edited. Return to %slisting%s', '<a href="'.$API->app_path('perch_shop') .'/statuses">', '</a>');
		}else{
		    $message = $HTML->failure_message('Sorry, that update was not successful.');
		}

	}

	if (PerchUtil::get('created') && !$message) {
	    $message = $HTML->success_message('Your status has been successfully created. Return to %s listing%s', '<a href="'. $API->app_path('perch_shop') .'/statuses">', '</a>');
	}


	if (is_object($Status)) {
		$details = $Status->to_array();
	}