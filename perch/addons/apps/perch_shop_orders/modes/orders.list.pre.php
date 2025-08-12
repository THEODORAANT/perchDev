<?php
	$Paging = $API->get('Paging');
	$Paging->set_per_page(24);

	$Orders   = new PerchShop_Orders($API);
	$Statuses = new PerchShop_OrderStatuses($API);
	$orders   = $Orders->get_admin_listing($Statuses->get_status_and_above('paid'), $Paging);

