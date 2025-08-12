<?php
	$Paging = $API->get('Paging');
	$Paging->set_per_page(24);

	$Promotions   = new PerchShop_Promotions($API);
	$promos   = $Promotions->all($Paging);

