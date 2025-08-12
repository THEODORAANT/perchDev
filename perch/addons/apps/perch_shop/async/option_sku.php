<?php
	include(__DIR__.'/../../../../core/inc/api.php');

	$API  = new PerchAPI(1.0, 'perch_shop');

	$OptionValues = new PerchShop_OptionValues($API);

	$existing = explode(',', PerchUtil::get('opts', ''));

	echo $OptionValues->get_unique_sku_code(PerchUtil::get('value'), PerchUtil::get('id'), $existing);