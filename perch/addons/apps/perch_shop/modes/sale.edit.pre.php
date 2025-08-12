<?php
	if (!PERCH_RUNWAY) PerchUtil::redirect('../');

	$Sales = new PerchShop_Sales($API);
	
	$edit_mode = false;
	$Sale     = false;
	$shop_id = false;
	$message   = false;
	$details   = false;

	if (PerchUtil::get('id')) {

		if (!$CurrentUser->has_priv('perch_shop.sales.edit')) {
		    PerchUtil::redirect($API->app_path());
		}

		$shop_id = PerchUtil::get('id');
		$Sale     = $Sales->find($shop_id);
		$edit_mode = true;

	}else{
		if (!$CurrentUser->has_priv('perch_shop.sales.create')) {
		    PerchUtil::redirect($API->app_path());
		}
	}

	// Template
	$Template   = $API->get('Template');
	$Template->set('shop/promotions/sale.html', 'shop');
	$tags = $Template->find_all_tags_and_repeaters();

	$Form = $API->get('Form');
	$Form->handle_empty_block_generation($Template);

	$Form->set_required_fields_from_template($Template, $details);

	if ($Form->submitted()) {

		$data = $Form->get_posted_content($Template, $Sales, $Sale);
		
		if ($Sale) {
			$Sale->update($data);	
			$Sale->index($Template);
		}else{
			$Sale = $Sales->create($data);
			

			if ($Sale) {
				$Sale->index($Template);
				PerchUtil::redirect($Perch->get_page().'?id='.$Sale->id().'&created=1');	
			}
			
		}

		if (is_object($Sale)) {
		    $message = $HTML->success_message('Your sale has been successfully edited. Return to %slisting%s', '<a class="notification-link" href="'.$API->app_path('perch_shop') .'/promos/sales">', '</a>');
		}else{
		    $message = $HTML->failure_message('Sorry, that update was not successful.');
		}

	}

	if (PerchUtil::get('created') && !$message) {
	    $message = $HTML->success_message('Your sale has been successfully created. Return to %s listing%s', '<a class="notification-link" href="'. $API->app_path('perch_shop') .'/promos/sales">', '</a>');
	}


	if (is_object($Sale)) {
		$details = $Sale->to_array();
	}