<?php
	$Paging = $API->get('Paging');
	$Paging->set_per_page(24);

	$Currencies   = new PerchShop_Currencies($API);
	$currencies   = $Currencies->all($Paging);

	if (!PerchUtil::count($currencies)) {
		$currencies   = $Currencies->all($Paging);
	}