<?php
	$Paging = $API->get('Paging');
	$Paging->set_per_page(24);

	$Options  = new PerchShop_Options($API);
	
	$options  = $Options->all($Paging);
