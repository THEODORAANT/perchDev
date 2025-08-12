<?php
	$Brands = new PerchShop_Brands($API);
	
	$Brand     = false;
	$shop_id = false;
	$details   = false;

	if (PerchUtil::get('id')) {

		if (!$CurrentUser->has_priv('perch_shop.customers.delete')) {
		    PerchUtil::redirect($API->app_path());
		}

		$shop_id = PerchUtil::get('id');
		$Brand     = $Brands->find($shop_id);
	}else{
		PerchUtil::redirect($API->app_path());
	}

	// Template
	$Template   = $API->get('Template');
	$Template->set('shop/brands/brand.html', 'shop');
	$tags = $Template->find_all_tags_and_repeaters();

	$Form = $API->get('Form');
	$Form->set_name('delete');
	
	if ($Form->submitted()) {

	
		if ($Brand) {
			$Brand->delete();	
		}

		if ($Form->submitted_via_ajax) {
		    echo $API->app_path('perch_shop_products').'/brands/';
		    exit;
		}else{
		   PerchUtil::redirect($API->app_path('perch_shop_products').'/brands/'); 
		}
			
			
	}

