<?php
	$Products = new PerchShop_Products($API);
	$Variations = new PerchShop_Variations($API);
	
	$edit_mode  	= false;
	$Product    	= false;
	$Variation    	= false;
	$shop_id  	= false;
	$message		= false;
	$details 		= false;

	if (PerchUtil::get('id')) {

		if (!$CurrentUser->has_priv('perch_shop.products.edit')) {
		    PerchUtil::redirect($API->app_path());
		}

		$shop_id 		   = PerchUtil::get('id');
		$Variation 		   = $Variations->find($shop_id);
		$Product           = $Products->find($Variation->productID());
		$edit_mode         = true;

	}

	if (PerchUtil::get('pid')) {

		if (!$CurrentUser->has_priv('perch_shop.products.edit')) {
		    PerchUtil::redirect($API->app_path());
		}

		$Product           = $Products->find(PerchUtil::get('pid'));
		$edit_mode         = true;

	}

	$Variations->set_productID($Product->id());


	// Template
	$Template   = $API->get('Template');
	$Template->set('shop/products/modifier.html', 'shop');
	$tags = $Template->find_all_tags_and_repeaters();

	$Form = $API->get('Form');
	$Form->handle_empty_block_generation($Template);

	$Form->set_required_fields_from_template($Template, $details);

	if ($Form->submitted()) {

		$data = $Form->get_posted_content($Template, $Variations, $Variation);
		$data['productID'] = $Product->id();

		if (is_object($Variation)) {
			$Variation->update($data);
			$Variation->index($Template);	
		}else{

			$Variation = $Variations->create($data);
			
			if ($Variation) {
				$Variation->index($Template);
				PerchUtil::redirect($Perch->get_page().'?id='.$Variation->id().'&created=1');	
			}
			
		}

		if (is_object($Variation)) {
		    $message = $HTML->success_message('Your product has been successfully edited. Return to %sproduct listing%s', '<a href="'.$API->app_path('perch_shop_products') .'">', '</a>');
		}else{
		    $message = $HTML->failure_message('Sorry, that update was not successful.');
		}

	}


	if (PerchUtil::get('created') && !$message) {
	    $message = $HTML->success_message('Your product has been successfully created. Return to %sproduct listing%s', '<a href="'. $API->app_path('perch_shop_products') .'">', '</a>');
	}


	if (is_object($Variation)) {
		$details = $Variation->to_array();
	}