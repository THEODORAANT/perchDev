<?php
	$Paging = $API->get('Paging');
	$Paging->set_per_page(24);

	$Countries   = new PerchShop_Countries($API);
	$countries   = $Countries->all($Paging);

	if (!PerchUtil::count($countries)) {
		$countries   = $Countries->all($Paging);
	}