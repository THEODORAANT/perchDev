<?php
	$Paging = $API->get('Paging');
	$Paging->set_per_page(24);

	$Emails   = new PerchShop_Emails($API);
	$emails   = $Emails->all($Paging);

	if (!PerchUtil::count($emails)) {
		$emails   = $Emails->all($Paging);
	}

