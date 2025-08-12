<?php

	function perch_shop_brands($opts=array(), $return=false)
	{
		$opts = PerchUtil::extend([
			'template'   => 'brands/list.html',
			'sort'       => 'title',
			'sort-order' => 'ASC',
			'cache'      => true,
			'cache-ttl'  => 900,
			'skip-template' => false,
		], $opts);

		if ($opts['skip-template']) $return = true;

		$ShopRuntime = PerchShop_Runtime::fetch();
		$r = $ShopRuntime->get_custom('Brands', $opts);

		if ($return) return $r;
		echo $r;
		PerchUtil::flush_output();
	}

	function perch_shop_brand($slug, $opts=array(), $return=false)
	{
		$opts = PerchUtil::extend([
				'template'   => 'brands/brand.html',
				'cache'      => true,
				'cache-ttl'  => 900,
				'filter'	 => 'slug',
				'match'		 => 'eq',
				'value'		 => $slug,
				'skip-template' => false,
		], $opts);

		if ($opts['skip-template']) $return = true;

		$ShopRuntime = PerchShop_Runtime::fetch();
		$r = $ShopRuntime->get_custom('Brands', $opts);

		if ($return) return $r;
		echo $r;
		PerchUtil::flush_output();
	}