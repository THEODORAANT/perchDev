<?php
	$message = false;

	$productID = PerchUtil::get('id');

	$Products   = new PerchShop_Products($API);
	$Product    = $Products->find($productID);

	$Files  = new PerchShop_ProductFiles($API);
	
	
	$files  = $Files->get_by_product_for_admin($productID);


