<?php
	$API    = new PerchAPI(1.0, 'core');
	$Lang   = $API->get('Lang');
	$HTML   = $API->get('HTML');
	$Paging = $API->get('Paging');

	$Paging->set_per_page(24);
    $id = PerchUtil::get('id');
	$MenuItemFeatutes = new PerchMenuItemFeatures;
	$top_level_features = $MenuItemFeatutes->get_for_parent($id ,$Paging);


?>
