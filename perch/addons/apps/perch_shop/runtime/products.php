<?php

	function perch_shop_products($opts=array(), $return=false)
	{
		$opts = PerchUtil::extend([
				'template'      => 'products/list.html',
				'sort'          => 'title',
				'sort-order'    => 'ASC',
				'cache'         => true,
				'cache-ttl'     => 900,
				'skip-template' => false,
				'variants'		=> false,
			], $opts);

		if ($opts['skip-template']) $return = true;

		$ShopRuntime = PerchShop_Runtime::fetch();
		$r = $ShopRuntime->get_custom('Products', $opts);

		if ($return) return $r;
		echo $r;
		PerchUtil::flush_output();
	}

	function perch_shop_product($slug, $opts=array(), $return=false)
	{
		$opts = PerchUtil::extend([
			'template'      => 'products/product.html',
			'cache'         => true,
			'cache-ttl'     => 900,
			'filter'        => 'slug',
			'match'         => 'eq',
			'value'         => $slug,
			'skip-template' => false,
			'variants'		=> false,
		], $opts);

		if ($opts['skip-template']) $return = true;

		$ShopRuntime = PerchShop_Runtime::fetch();
		$r = $ShopRuntime->get_custom('Products', $opts);

		if ($return) return $r;
		echo $r;
		PerchUtil::flush_output();
	}

	function perch_shop_product_variants($slug, $opts=array(), $return=false)
	{

		$ShopRuntime = PerchShop_Runtime::fetch();

		$parentID = $ShopRuntime->get_product_id($slug);

		$opts = PerchUtil::extend([
			'template'      => 'products/variant_list.html',
			'sort'          => 'title',
			'sort-order'    => 'ASC',
			'cache'         => true,
			'cache-ttl'     => 900,
			'filter'        => 'parentID',
			'match'         => 'eq',
			'value'         => $parentID,
			'skip-template' => false,
			'variants'		=> false,
		], $opts);

		if ($opts['skip-template']) $return = true;

		$ShopRuntime = PerchShop_Runtime::fetch();

		$r = $ShopRuntime->get_custom('ProductVariants', $opts);

		if ($return) return $r;
		echo $r;
		PerchUtil::flush_output();
	}

	function perch_shop_purchased_files($opts=array(), $return=false)
	{
		$opts = PerchUtil::extend([
				'template'      => 'products/files_list.html',
				'skip-template' => false,
				'order'	 	    => null,
			], $opts);

		if ($opts['skip-template']) $return = true;

		$ShopRuntime = PerchShop_Runtime::fetch();
		$r = $ShopRuntime->get_files($opts);

		if ($return) return $r;
		echo $r;
		PerchUtil::flush_output();
	}

	function perch_shop_customer_has_purchased_file($fileID)
	{
		$ShopRuntime = PerchShop_Runtime::fetch();
		return $ShopRuntime->customer_has_purchased_file($fileID);
	}

	function perch_shop_download_file($fileID) 
	{
		$ShopRuntime = PerchShop_Runtime::fetch();

		if (perch_member_logged_in() && $ShopRuntime->customer_has_purchased_file($fileID)) {
		    
		    list($file, $bucket) = $ShopRuntime->get_file_path_and_bucket($fileID);
		    perch_members_secure_download($file, $bucket);
		}else{

		}
	}