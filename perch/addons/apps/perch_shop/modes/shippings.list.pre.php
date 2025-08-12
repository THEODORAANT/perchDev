<?php
	$Paging = $API->get('Paging');
	$Paging->set_per_page(24);

	$Shippings   = new PerchShop_Shippings($API);
	$shippings   = $Shippings->all($Paging);

	if (!PerchUtil::count($shippings)) {
		$shippings   = $Shippings->all($Paging);
	}