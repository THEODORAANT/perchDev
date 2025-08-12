<?php
	$Customers = new PerchShop_Customers($API);

	$edit_mode = false;
	$Customer     = false;
	$shop_id = false;
	$message   = false;
	$details   = false;

	if (PerchUtil::get('id')) {

		if (!$CurrentUser->has_priv('perch_shop.customers.edit')) {
		    PerchUtil::redirect($API->app_path());
		}

		$shop_id = PerchUtil::get('id');
		$Customer     = $Customers->find($shop_id);
		$edit_mode = true;

	}else{
		if (!$CurrentUser->has_priv('perch_shop.customers.create')) {
		    PerchUtil::redirect($API->app_path());
		}
	}

	// Template
	$Template   = $API->get('Template');
	$Template->set('shop/customers/customer.html', 'shop');
	$tags = $Template->find_all_tags_and_repeaters();

	$Form = $API->get('Form');
	$Form->handle_empty_block_generation($Template);

	$Form->set_required_fields_from_template($Template, $details);

	if ($Form->submitted()) {

		$data = $Form->get_posted_content($Template, $Customers, $Customer);

		if ($Customer) {
			$Customer->update($data);
			$Customer->index($Template);
		}else{
			$Customer = $Customers->create($data);
			$Customer->index($Template);

			if ($Customer) {
				PerchUtil::redirect($Perch->get_page().'?id='.$Customer->id().'&created=1');
			}

		}

		if (is_object($Customer)) {
		    $message = $HTML->success_message('Your customer has been successfully edited. Return to %slisting%s', '<a href="'.$API->app_path('perch_shop_orders') .'/customers">', '</a>');
		}else{
		    $message = $HTML->failure_message('Sorry, that update was not successful.');
		}

	}

	if (PerchUtil::get('created') && !$message) {
	    $message = $HTML->success_message('Your customer has been successfully created. Return to %s listing%s', '<a href="'. $API->app_path('perch_shop_orders') .'/customers">', '</a>');
	}


	if (is_object($Customer)) {
		$details = $Customer->to_array();
	}