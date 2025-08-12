<?php
    if (!$CurrentUser->has_priv('perch_shop.products.edit')) {
        PerchUtil::redirect($API->app_path());
    }

    $Files = new PerchShop_ProductFiles($API);
    $Products = new PerchShop_Products($API);

    $HTML = $API->get('HTML');
    $Form = $API->get('Form');

    $Form->set_name('delete');

	
	$message = false;


    if (PerchUtil::get('id')) {
        $fileID    = PerchUtil::get('id');
        $File      = $Files->find($fileID);
        $Product   = $Products->find($File->productID()); 
    }else{
	    PerchUtil::redirect($API->app_path());
	}
	

    if ($Form->submitted()) {
	
    	if (is_object($File)) {
    	    $File->delete();
    
    	    if ($Form->submitted_via_ajax) {
    	        echo $API->app_path('perch_shop_products').'/product/files/?id='.$Product->id();
    	        exit;
    	    }else{
    	       PerchUtil::redirect($API->app_path('perch_shop_products').'/product/files/?id='.$Product->id()); 
    	    }

        }else{
            $message = $HTML->failure_message('Sorry, that file could not be deleted.');
        }
    }
