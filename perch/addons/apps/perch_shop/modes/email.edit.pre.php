<?php
	$Emails = new PerchShop_Emails($API);

	$edit_mode = false;
	$Email     = false;
	$shop_id = false;
	$message   = false;
	$details   = false;

	if (PerchUtil::get('id')) {

		if (!$CurrentUser->has_priv('perch_shop.promos.edit')) {
		    PerchUtil::redirect($API->app_path());
		}

		$shop_id = PerchUtil::get('id');
		$Email     = $Emails->find($shop_id);
		$edit_mode = true;

	}else{
		if (!$CurrentUser->has_priv('perch_shop.promos.create')) {
		    PerchUtil::redirect($API->app_path());
		}
	}

	// Template
	$Template   = $API->get('Template');

	if ($Email) {
		$Template->set('shop/emails/'.$Email->emailTemplate(), 'shop');
	}else{
		$Template->set('shop/emails/email.html', 'shop');
	}

	$tags = $Template->find_all_tags_and_repeaters();

	$Form = $API->get('Form');
	$Form->handle_empty_block_generation($Template);

	$Form->set_required_fields_from_template($Template, $details);	


	if ($Form->submitted()) {

		$data = $Form->get_posted_content($Template, $Emails, $Email);

		if ($Email) {
			$Email->update($data);
			$Email->index($Template);
		}else{
			$Email = $Emails->create($data);


			if ($Email) {
				$Email->index($Template);
				PerchUtil::redirect($Perch->get_page().'?id='.$Email->id().'&created=1');
			}

		}

		if (is_object($Email)) {
		    $message = $HTML->success_message('Your email has been successfully edited. Return to %slisting%s', '<a href="'.$API->app_path('perch_shop') .'/emails">', '</a>');
		}else{
		    $message = $HTML->failure_message('Sorry, that update was not successful.');
		}

	}

	if (PerchUtil::get('created') && !$message) {
	    $message = $HTML->success_message('Your email has been successfully created. Return to %s listing%s', '<a href="'. $API->app_path('perch_shop') .'/emails">', '</a>');
	}


	if (is_object($Email)) {
		$details = $Email->to_array();
	}