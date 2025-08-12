<?php
	$Products = new PerchShop_Products($API);
	
	$edit_mode  	= false;
	$Product    	= false;
	$shop_id  	= false;
	$message		= false;
	$details 		= false;

	if (PerchUtil::get('id')) {

		if (!$CurrentUser->has_priv('perch_shop.products.edit')) {
		    PerchUtil::redirect($API->app_path());
		}

		$shop_id = PerchUtil::get('id');
		$Product           = $Products->find($shop_id);
		$edit_mode         = true;

	}else{
		if (!$CurrentUser->has_priv('perch_shop.products.create')) {
		    PerchUtil::redirect($API->app_path());
		}
	}

	// Template
	$Template   = $API->get('Template');

	if (is_object($Product) && $Product->is_variant()) {
		$Template->set('shop/products/variant.html', 'shop');
	}else{
		$Template->set('shop/products/product.html', 'shop');
	}

	$tags = $Template->find_all_tags_and_repeaters();

	$Form = $API->get('Form');
	$Form->handle_empty_block_generation($Template);

	$Form->set_required_fields_from_template($Template, $details);


	if ($Form->submitted()) {

		$data		 = $Form->get_posted_content($Template, $Products, $Product);
		$search_text = $Form->get_search_text();

		if (is_object($Product)) {
			$Product->update($data);
			$Product->index($Template);	
			$Product->update_search_text($search_text);
		}else{

			$Product = $Products->create($data);
			
			if ($Product) {
				$Product->index($Template);
				$Product->update_search_text($search_text);
				PerchUtil::redirect($Perch->get_page().'?id='.$Product->id().'&created=1');	
			}
			
		}

		if (is_object($Product)) {
		    $message = $HTML->success_message('Your product has been successfully edited. Return to %sproduct listing%s', '<a href="'.$API->app_path('perch_shop_products') .'" class="notification-link">', '</a>');
		}else{
		    $message = $HTML->failure_message('Sorry, that update was not successful.');
		}

	}



	if (PerchUtil::get('created') && !$message) {
	    $message = $HTML->success_message('Your product has been successfully created. Return to %sproduct listing%s', '<a href="'. $API->app_path('perch_shop_products') .'" class="notification-link">', '</a>');
	}

	if (is_object($Product)) {
		$details = $Product->to_array();
	}

