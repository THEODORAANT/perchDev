<?php
	$TaxGroups = new PerchShop_TaxGroups($API);
	$TaxRates  = new PerchShop_TaxRates($API);

	$Locations = new PerchShop_TaxLocations($API);
	$locations = $Locations->all();

	$edit_mode   = false;
	$TaxGroup    = false;
	$shop_id     = false;
	$message     = false;
	$details     = false;

	if (PerchUtil::get('id')) {

		if (!$CurrentUser->has_priv('perch_shop.taxgroups.edit')) {
		    PerchUtil::redirect($API->app_path());
		}

		$shop_id = PerchUtil::get('id');
		$TaxGroup     = $TaxGroups->find($shop_id);
		$edit_mode = true;

	}else{
		if (!$CurrentUser->has_priv('perch_shop.taxgroups.create')) {
		    PerchUtil::redirect($API->app_path());
		}
	}

	// Template
	$Template   = $API->get('Template');
	$Template->set('shop/tax/group.html', 'shop');
	$tags = $Template->find_all_tags_and_repeaters();

	$Form = $API->get('Form');
	$Form->handle_empty_block_generation($Template);

	$Form->set_required_fields_from_template($Template, $details);

	if ($Form->submitted()) {

		$rate_data = $Form->find_items('loc_');

		$created = false;

		$data = $Form->get_posted_content($Template, $TaxGroups, $TaxGroup);

		if ($TaxGroup) {
			$TaxGroup->update($data);
			$TaxGroup->index($Template);
		}else{
			$TaxGroup = $TaxGroups->create($data);

			if ($TaxGroup) {
				$created = true;
				$TaxGroup->index($Template);
			}

		}

		if (is_object($TaxGroup)) {

			// Rates
			if (PerchUtil::count($rate_data)) {
				foreach($rate_data as $locationID=>$rateID) {
					$rate = [
						'groupID'    => $TaxGroup->id(),
						'locationID' => $locationID,
						'rateID'     => $rateID,
					];
					$TaxRates->add_or_update($rate);
				}
			}



		    $message = $HTML->success_message('Your tax group has been successfully edited. Return to %slisting%s', '<a href="'.$API->app_path('perch_shop') .'/tax">', '</a>');

		    if ($created) {
		    	PerchUtil::redirect($Perch->get_page().'?id='.$TaxGroup->id().'&created=1');
		    }
		}else{
		    $message = $HTML->failure_message('Sorry, that update was not successful.');
		}

	}

	if (PerchUtil::get('created') && !$message) {
	    $message = $HTML->success_message('Your tax group has been successfully created. Return to %s listing%s', '<a href="'. $API->app_path('perch_shop') .'/tax">', '</a>');
	}


	if (is_object($TaxGroup)) {
		$details = $TaxGroup->to_array();
	}