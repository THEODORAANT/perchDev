<?php
	$Paging = $API->get('Paging');
	$Paging->set_per_page(24);

	$Zones   = new PerchShop_ShippingZones($API);
	$zones   = $Zones->all($Paging);

	if (!PerchUtil::count($zones)) {
		$zones   = $Zones->all($Paging);
	}