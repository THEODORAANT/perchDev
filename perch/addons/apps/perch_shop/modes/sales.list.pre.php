<?php
	if (!PERCH_RUNWAY) PerchUtil::redirect('../');

	$Paging = $API->get('Paging');
	$Paging->set_per_page(24);

	$Sales   = new PerchShop_Sales($API);
	$sales   = $Sales->all($Paging);

