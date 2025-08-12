<?php
	$Paging = $API->get('Paging');
	$Paging->set_per_page(24);

	$Brands   = new PerchShop_Brands($API);
	$brands   = $Brands->all($Paging);

	if (!PerchUtil::count($brands)) {
		$brands   = $Brands->all($Paging);
	}