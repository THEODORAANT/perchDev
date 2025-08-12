<?php
	$Options = new PerchShop_Options($API);
	$OptionValues   = new PerchShop_OptionValues($API);
	
	$edit_mode  	= false;
	$Option    	= false;
	$shop_id  		= false;
	$message		= false;
	$details 		= false;

	if (PerchUtil::get('id')) {

		if (!$CurrentUser->has_priv('perch_shop.options.edit')) {
		    PerchUtil::redirect($API->app_path());
		}

		$shop_id 		   = PerchUtil::get('id');
		$Option 		   = $Options->find($shop_id);
		$edit_mode         = true;
	}

	// Template
	$Template   = $API->get('Template');
	$Template->set('shop/products/option.html', 'shop');
	$tags = $Template->find_all_tags_and_repeaters();

	$Form = $API->get('Form');
	$Form->handle_empty_block_generation($Template);

	$Form->set_required_fields_from_template($Template, $details);

	if ($Form->submitted()) {

		$data = $Form->get_posted_content($Template, $Options, $Option);
		$optionValues = [];
		$created = false;

		if (isset($data['optionDynamicFields'])) {
			$dyn = PerchUtil::json_safe_decode($data['optionDynamicFields'], true);
			if (isset($dyn['options'])) {
				$optionValues = $dyn['options'];
				unset($dyn['options']);
				$data['optionDynamicFields'] = PerchUtil::json_safe_encode($dyn);
			}
		}

		if (is_object($Option)) {
			$Option->update($data);
			$Option->index($Template);
		}else{
			$Option = $Options->create($data);
			$created = true;
		}

		if (is_object($Option)) {

			$i = 1;

			$existing_value_ids = $Option->get_value_ids();
			#PerchUtil::debug($existing_value_ids, 'success');

			// OPTIONS
			foreach($optionValues as $optionValue) {
				PerchUtil::debug($optionValue);

				$opt_data = [
					'optionID' => $Option->id(),
					'valueTitle' => $optionValue['title'],
					'valueSKUCode' => $optionValue['skucode'],
					'valueOrder' => $i,
					'valueDynamicFields' => '{}',
				];

				if ($optionValue['id']!='') {

					// remove this ID from the known IDs list
					$existing_value_ids = array_diff($existing_value_ids, [(int)$optionValue['id']]);

					$OptionValue = $OptionValues->find($optionValue['id']);
					$OptionValue->update($opt_data);
				}else{
					$OptionValue = $OptionValues->create($opt_data);
				}

				$i++;
			}

			// delete options
			if (PerchUtil::count($existing_value_ids)) {
				// if there are any IDs left in this array then they've been deleted.
				foreach($existing_value_ids as $id) {
					$OptionValue = $OptionValues->find($id);
					if ($OptionValue) $OptionValue->delete();
				}
			}

		    $message = $HTML->success_message('Your product option has been successfully edited. Return to %soption listing%s', '<a href="'.$API->app_path('perch_shop_products') .'/options/">', '</a>');
		}else{
		    $message = $HTML->failure_message('Sorry, that update was not successful.');
		}

		if ($created && $Option) {
			$Option->index($Template);
			PerchUtil::redirect($Perch->get_page().'?id='.$Option->id().'&created=1');	
		}

	}


	if (PerchUtil::get('created') && !$message) {
	    $message = $HTML->success_message('Your product option has been successfully created. Return to %soption listing%s', '<a href="'. $API->app_path('perch_shop_products') .'/options/">', '</a>');
	}


	if (is_object($Option)) {
		$details = $Option->to_array();
	}