<?php
	$Paging = $API->get('Paging');
	$Paging->set_per_page(24);

	$Customers   = new PerchShop_Customers($API);
	$customers   = $Customers->all($Paging);

