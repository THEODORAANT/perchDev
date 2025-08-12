<?php

	$Form 	= new PerchForm('reorder', false);

    if ($Form->posted() && $Form->validate()) {

		if ($Form->submitted_via_ajax) {
			$Settings->set('perch_shop_dashboard_order', $_POST['order'], $CurrentUser->id());
    	    echo $Form->get_token();
    	    exit;
    	}

    }

	// Prompt an install
	$Paging = $API->get('Paging');
	$Paging->set_per_page(1);

	$Products   = new PerchShop_Products($API);
	$products   = $Products->all($Paging);

    $first_run = false;

	if (!PerchUtil::count($products)) {   
		$Products->attempt_install();
	
        $first_run = true;
    }
	// end prompt an install

	// Try to update
    #$Settings = $API->get('Settings');

    #if ($Settings->get('perch_shop_update')->val()!='g33') {
    #    include('update.php');
    #}

    if (!$first_run) {
        $Stats = new PerchShop_Stats($API);

        $stats = false;
        $stats = $Stats->get_store_stats();
        #PerchUtil::debug($stats);       
    }

    $default_widget_order = 'revenue,customers,orders';