<?php

	$Paging = $API->get('Paging');
	$Paging->set_per_page(24);

	$TaxBands   = new PerchShop_TaxBands($API);
	$taxbands   = $TaxBands->all($Paging);

	if (!PerchUtil::count($taxbands)) {
		$TaxBands->attempt_install();
		$taxbands   = $TaxBands->all($Paging);
	}