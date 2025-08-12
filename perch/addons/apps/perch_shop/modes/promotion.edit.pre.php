<?php
	$Promotions = new PerchShop_Promotions($API);
	
	$edit_mode = false;
	$Promotion     = false;
	$shop_id = false;
	$message   = false;
	$details   = false;

	if (PerchUtil::get('id')) {

		if (!$CurrentUser->has_priv('perch_shop.promos.edit')) {
		    PerchUtil::redirect($API->app_path());
		}

		$shop_id = PerchUtil::get('id');
		$Promotion     = $Promotions->find($shop_id);
		$edit_mode = true;

	}else{
		if (!$CurrentUser->has_priv('perch_shop.promos.create')) {
		    PerchUtil::redirect($API->app_path());
		}
	}

	// Template
	$Template   = $API->get('Template');
	$Template->set('shop/promotions/promotion.html', 'shop');
	$tags = $Template->find_all_tags_and_repeaters();

	$Form = $API->get('Form');
	$Form->handle_empty_block_generation($Template);

	$Form->set_required_fields_from_template($Template, $details);

	if ($Form->submitted()) {

		$data = $Form->get_posted_content($Template, $Promotions, $Promotion);
		
		if ($Promotion) {
			$Promotion->update($data);	
			$Promotion->index($Template);
		}else{
			$Promotion = $Promotions->create($data);
			

			if ($Promotion) {
				$Promotion->index($Template);
				PerchUtil::redirect($Perch->get_page().'?id='.$Promotion->id().'&created=1');	
			}
			
		}

		if (is_object($Promotion)) {
		    $message = $HTML->success_message('Your promotion has been successfully edited. Return to %slisting%s', '<a href="'.$API->app_path('perch_shop') .'/promos">', '</a>');
		}else{
		    $message = $HTML->failure_message('Sorry, that update was not successful.');
		}

	}

	if (PerchUtil::get('created') && !$message) {
	    $message = $HTML->success_message('Your promotion has been successfully created. Return to %s listing%s', '<a href="'. $API->app_path('perch_shop') .'/promos">', '</a>');
	}


	if (is_object($Promotion)) {
		$details = $Promotion->to_array();
	}