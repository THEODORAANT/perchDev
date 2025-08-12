<?php
	$Paging = $API->get('Paging');
	$Paging->set_per_page(24);

	$Statuses   = new PerchShop_OrderStatuses($API);
	$statuses   = $Statuses->all($Paging);

	if (!PerchUtil::count($statuses)) {
		$statuses   = $Statuses->all($Paging);
	}