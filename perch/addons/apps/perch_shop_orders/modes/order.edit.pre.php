<?php
	$Brands = new PerchShop_Brands($API);
	
	$edit_mode = false;
	$Brand     = false;
	$shop_id = false;
	$message   = false;
	$details   = false;

	if (PerchUtil::get('id')) {

		if (!$CurrentUser->has_priv('perch_shop.brands.edit')) {
		    PerchUtil::redirect($API->app_path());
		}

		$shop_id = PerchUtil::get('id');
		$Brand     = $Brands->find($shop_id);
		$edit_mode = true;

	}else{
		if (!$CurrentUser->has_priv('perch_shop.brands.create')) {
		    PerchUtil::redirect($API->app_path());
		}
	}

	// Template
	$Template   = $API->get('Template');
	$Template->set('shop/brands/brand.html', 'shop');
	$tags = $Template->find_all_tags_and_repeaters();

	$Form = $API->get('Form');
	$Form->handle_empty_block_generation($Template);

	$Form->set_required_fields_from_template($Template, $details);

	if ($Form->submitted()) {

		$data = $Form->get_posted_content($Template, $Brands, $Brand);
		
		if ($Brand) {
			$Brand->update($data);	
			$Brand->index($Template);
		}else{
			$Brand = $Brands->create($data);
			$Brand->index($Template);

			if ($Brand) {
				PerchUtil::redirect($Perch->get_page().'?id='.$Brand->id().'&created=1');	
			}
			
		}

		if (is_object($Brand)) {
		    $message = $HTML->success_message('Your brand has been successfully edited. Return to %slisting%s', '<a href="'.$API->app_path('perch_shop_products') .'/brands">', '</a>');
		}else{
		    $message = $HTML->failure_message('Sorry, that update was not successful.');
		}

	}

	if (PerchUtil::get('created') && !$message) {
	    $message = $HTML->success_message('Your brand has been successfully created. Return to %s listing%s', '<a href="'. $API->app_path('perch_shop_products') .'/brands">', '</a>');
	}


	if (is_object($Brand)) {
		$details = $Brand->to_array();
	}