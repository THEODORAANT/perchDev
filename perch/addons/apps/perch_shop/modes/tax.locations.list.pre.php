<?php
	$Paging = $API->get('Paging');
	$Paging->set_per_page(24);

	$Locations = new PerchShop_TaxLocations($API);
	$locations = $Locations->all($Paging);