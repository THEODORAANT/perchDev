<?php
	$Currencies = new PerchShop_Currencies($API);
	
	$edit_mode = false;
	$Currency     = false;
	$shop_id = false;
	$message   = false;
	$details   = false;

	if (PerchUtil::get('id')) {

		if (!$CurrentUser->has_priv('perch_shop.currencies.edit')) {
		    PerchUtil::redirect($API->app_path());
		}

		$shop_id = PerchUtil::get('id');
		$Currency     = $Currencies->find($shop_id);
		$edit_mode = true;

	}else{
		if (!$CurrentUser->has_priv('perch_shop.currencies.create')) {
		    PerchUtil::redirect($API->app_path());
		}
	}

	// Template
	$Template   = $API->get('Template');
	$Template->set('shop/currencies/currency.html', 'shop');
	$tags = $Template->find_all_tags_and_repeaters();

	$Form = $API->get('Form');
	$Form->handle_empty_block_generation($Template);

	$Form->set_required_fields_from_template($Template, $details);

	if ($Form->submitted()) {

		$data = $Form->get_posted_content($Template, $Currencies, $Currency);
		
		if ($Currency) {
			$Currency->update($data);	
			$Currency->index($Template);
		}else{
			$Currency = $Currencies->create($data);
			$Currency->index($Template);

			if ($Currency) {
				PerchUtil::redirect($Perch->get_page().'?id='.$Currency->id().'&created=1');	
			}
			
		}

		if (is_object($Currency)) {
		    $message = $HTML->success_message('Your currency has been successfully edited. Return to %slisting%s', '<a href="'.$API->app_path('perch_shop') .'/currencies">', '</a>');
		}else{
		    $message = $HTML->failure_message('Sorry, that update was not successful.');
		}

	}

	if (PerchUtil::get('created') && !$message) {
	    $message = $HTML->success_message('Your currency has been successfully created. Return to %s listing%s', '<a href="'. $API->app_path('perch_shop') .'/currencies">', '</a>');
	}


	if (is_object($Currency)) {
		$details = $Currency->to_array();
	}