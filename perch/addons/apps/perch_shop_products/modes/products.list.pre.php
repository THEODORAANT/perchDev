<?php
	$Paging = $API->get('Paging');
	$Paging->set_per_page(24);

	$Products   = new PerchShop_Products($API);
	$products   = $Products->get_for_admin_listing($Paging);
	
	if (!PerchUtil::count($products)) {
		$Products->attempt_install();
	}


