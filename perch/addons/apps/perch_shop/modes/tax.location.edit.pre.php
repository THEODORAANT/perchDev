<?php
	$TaxLocations = new PerchShop_TaxLocations($API);
	$TaxRates = new PerchShop_TaxRates($API);

	$edit_mode   = false;
	$TaxLocation = false;
	$shop_id     = false;
	$message     = false;
	$details     = false;

	if (PerchUtil::get('id')) {

		if (!$CurrentUser->has_priv('perch_shop.taxlocations.edit')) {
		    PerchUtil::redirect($API->app_path());
		}

		$shop_id = PerchUtil::get('id');
		$TaxLocation     = $TaxLocations->find($shop_id);
		$edit_mode = true;

	}else{
		if (!$CurrentUser->has_priv('perch_shop.taxlocations.create')) {
		    PerchUtil::redirect($API->app_path());
		}
	}

	// Template
	$Template   = $API->get('Template');
	$Template->set('shop/tax/location.html', 'shop');
	$tags = $Template->find_all_tags_and_repeaters();

	$Form = $API->get('Form');
	$Form->handle_empty_block_generation($Template);

	$Form->set_required_fields_from_template($Template, $details);

	if ($Form->submitted()) {

		$data = $Form->get_posted_content($Template, $TaxLocations, $TaxLocation);

		$created = false;
		$rates = [];

		if (isset($data['locationDynamicFields'])) {
			$dyn = PerchUtil::json_safe_decode($data['locationDynamicFields'], true);
			if (isset($dyn['rates'])) {
				$rates = $dyn['rates'];
				unset($dyn['rates']);
				$data['locationDynamicFields'] = PerchUtil::json_safe_encode($dyn);
			}
		}

		PerchUtil::debug($rates);


		if ($TaxLocation) {
			$TaxLocation->update($data);
			$TaxLocation->index($Template);
		}else{
			$TaxLocation = $TaxLocations->create($data);
			$created = true;
		}

		// Tax rates
		if (is_object($TaxLocation)) {

			$existing_rate_ids = $TaxRates->get_rate_ids_for_location($TaxLocation->id());
			#PerchUtil::debug($existing_rate_ids, 'success');

			// RATES
			foreach($rates as $taxRate) {
				PerchUtil::debug($taxRate);

				$rate_data = [
					'locationID' => $TaxLocation->id(),
					'rateTitle' => $taxRate['title'],
					'rateValue' => $taxRate['rate'],
					'rateDynamicFields' => '{}',
				];

				if ($taxRate['id']!='') {

					// remove this ID from the known IDs list
					$existing_rate_ids = array_diff($existing_rate_ids, [(int)$taxRate['id']]);

					$TaxRate = $TaxRates->find($taxRate['id']);
					$TaxRate->update($rate_data);
				}else{
					$TaxRate = $TaxRates->create($rate_data);
				}
			}

			// delete options
			if (PerchUtil::count($existing_rate_ids)) {
				// if there are any IDs left in this array then they've been deleted.
				foreach($existing_rate_ids as $id) {
					$TaxRate = $TaxRates->find($id);
					if ($TaxRate) $TaxRate->delete();
				}
			}

		    $message = $HTML->success_message('Your tax location has been successfully edited. Return to %slisting%s', '<a href="'.$API->app_path('perch_shop') .'/tax/locations" class="notification-link">', '</a>');
		}else{
		    $message = $HTML->failure_message('Sorry, that update was not successful.');
		}


		if ($created && $TaxLocation) {
			$TaxLocation->index($Template);
			PerchUtil::redirect($Perch->get_page().'?id='.$TaxLocation->id().'&created=1');
		}

	}

	if (PerchUtil::get('created') && !$message) {
	    $message = $HTML->success_message('Your tax location has been successfully created. Return to %s listing%s', '<a href="'. $API->app_path('perch_shop') .'/tax/locations" class="notification-link">', '</a>');
	}


	if (is_object($TaxLocation)) {
		$details = $TaxLocation->to_array();
	}