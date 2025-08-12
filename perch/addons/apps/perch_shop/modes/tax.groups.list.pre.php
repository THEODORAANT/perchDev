<?php
	$Paging = $API->get('Paging');
	$Paging->set_per_page(24);

	$Groups = new PerchShop_TaxGroups($API);
	$groups = $Groups->all($Paging);