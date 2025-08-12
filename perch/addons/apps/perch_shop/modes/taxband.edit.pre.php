<?php
	$TaxBands = new PerchShop_TaxBands($API);

	$edit_mode = false;
	$TaxBand   = false;
	$shop_id = false;
	$message   = false;
	$details   = false;

	if (PerchUtil::get('id')) {

		if (!$CurrentUser->has_priv('perch_shop.taxbands.edit')) {
		    PerchUtil::redirect($API->app_path());
		}

		$shop_id = PerchUtil::get('id');
		$TaxBand     = $TaxBands->find($shop_id);
		$edit_mode = true;

	}else{
		if (!$CurrentUser->has_priv('perch_shop.taxbands.create')) {
		    PerchUtil::redirect($API->app_path());
		}
	}

	// Template
	$Template   = $API->get('Template');
	$Template->set('shop/taxbands/taxband.html', 'shop');
	$tags = $Template->find_all_tags_and_repeaters();

	$Form = $API->get('Form');
	$Form->handle_empty_block_generation($Template);

	$Form->set_required_fields_from_template($Template, $details);

	if ($Form->submitted()) {

		$data = $Form->get_posted_content($Template, $TaxBands, $TaxBand);

		if ($TaxBand) {
			$TaxBand->update($data);
			$TaxBand->index($Template);
		}else{
			$TaxBand = $TaxBands->create($data);
			$TaxBand->index($Template);

			if ($TaxBand) {
				PerchUtil::redirect($Perch->get_page().'?id='.$TaxBand->id().'&created=1');
			}

		}

		if (is_object($TaxBand)) {
		    $message = $HTML->success_message('Your tax band has been successfully edited. Return to %slisting%s', '<a href="'.$API->app_path('perch_shop') .'/taxbands">', '</a>');
		}else{
		    $message = $HTML->failure_message('Sorry, that update was not successful.');
		}

	}

	if (PerchUtil::get('created') && !$message) {
	    $message = $HTML->success_message('Your tax band has been successfully created. Return to %s listing%s', '<a href="'. $API->app_path('perch_shop') .'/taxbands">', '</a>');
	}


	if (is_object($TaxBand)) {
		$details = $TaxBand->to_array();
	}