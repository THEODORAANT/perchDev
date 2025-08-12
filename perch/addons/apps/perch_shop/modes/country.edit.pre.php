<?php
	$Countries = new PerchShop_Countries($API);
	
	$edit_mode = false;
	$Country     = false;
	$shop_id = false;
	$message   = false;
	$details   = false;

	if (PerchUtil::get('id')) {

		if (!$CurrentUser->has_priv('perch_shop.countries.edit')) {
		    PerchUtil::redirect($API->app_path());
		}

		$shop_id = PerchUtil::get('id');
		$Country     = $Countries->find($shop_id);
		$edit_mode = true;

	}else{
		if (!$CurrentUser->has_priv('perch_shop.countries.create')) {
		    PerchUtil::redirect($API->app_path());
		}
	}

	// Template
	$Template   = $API->get('Template');
	$Template->set('shop/countries/country.html', 'shop');
	$tags = $Template->find_all_tags_and_repeaters();

	$Form = $API->get('Form');
	$Form->handle_empty_block_generation($Template);

	$Form->set_required_fields_from_template($Template, $details);

	if ($Form->submitted()) {

		$data = $Form->get_posted_content($Template, $Countries, $Country);

		if (!isset($data['eu'])) $data['eu'] = 0;
		if (!isset($data['countryActive'])) $data['countryActive'] = 0;
		
		if ($Country) {
			$Country->update($data);	
			$Country->index($Template);
		}else{
			$Country = $Countries->create($data);
			$Country->index($Template);

			if ($Country) {
				PerchUtil::redirect($Perch->get_page().'?id='.$Country->id().'&created=1');	
			}
			
		}

		if (is_object($Country)) {
		    $message = $HTML->success_message('Your country has been successfully edited. Return to %slisting%s', '<a href="'.$API->app_path('perch_shop') .'/countries">', '</a>');
		}else{
		    $message = $HTML->failure_message('Sorry, that update was not successful.');
		}

	}

	if (PerchUtil::get('created') && !$message) {
	    $message = $HTML->success_message('Your country has been successfully created. Return to %s listing%s', '<a href="'. $API->app_path('perch_shop') .'/countries">', '</a>');
	}


	if (is_object($Country)) {
		$details = $Country->to_array();
	}