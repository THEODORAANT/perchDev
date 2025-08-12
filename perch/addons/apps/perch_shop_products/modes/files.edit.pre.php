<?php
	if (!$CurrentUser->has_priv('perch_shop.products.edit')) {
	    PerchUtil::redirect($API->app_path());
	}

	$Files = new PerchShop_ProductFiles($API);
	$Products = new PerchShop_Products($API);
	
	$edit_mode = false;
	$File      = false;
	$productID = false;
	$message   = false;
	$details   = false;

	if (PerchUtil::get('id')) {
		$fileID    = PerchUtil::get('id');
		$File      = $Files->find($fileID);
		$Product   = $Products->find($File->productID());

		$edit_mode = true;

	}elseif (PerchUtil::get('pid')) {
		
		$productID = PerchUtil::get('pid');
		$Product   = $Products->find($productID);

	}else{
	    PerchUtil::redirect($API->app_path());
	}

	// Template
	$Template   = $API->get('Template');
	$Template->set('shop/products/file.html', 'shop');
	$tags = $Template->find_all_tags_and_repeaters();

	$Form = $API->get('Form');
	$Form->handle_empty_block_generation($Template);

	$Form->set_required_fields_from_template($Template, $details);

	if ($Form->submitted()) {

		if (isset($_POST['perch_file_assetID'])) {
			$file_asset_id = $_POST['perch_file_assetID'];	
		}else{
			$file_asset_id = null;
		}
		

		$data = $Form->get_posted_content($Template, $Files, $File);

		$data['productID'] = $Product->id();
		$data['resourceID'] = $file_asset_id;
		
		if ($File) {
			$File->update($data);	
			$File->index($Template);
		}else{
			$File = $Files->create($data);
			$File->index($Template);

			if ($File) {
				PerchUtil::redirect($Perch->get_page().'?id='.$File->id().'&created=1');	
			}
			
		}

		if (is_object($File)) {
		    $message = $HTML->success_message('Your file has been successfully edited. Return to %slisting%s', '<a href="'.$API->app_path('perch_shop_products') .'/product/files/?id='.$Product->id().'">', '</a>');
		}else{
		    $message = $HTML->failure_message('Sorry, that update was not successful.');
		}

	}

	if (PerchUtil::get('created') && !$message) {
	    $message = $HTML->success_message('Your brand has been successfully created. Return to %s listing%s', '<a href="'. $API->app_path('perch_shop_products') .'/product/files/?id='.$Product->id().'">', '</a>');
	}


	if (is_object($File)) {
		$details = $File->to_array();
	}