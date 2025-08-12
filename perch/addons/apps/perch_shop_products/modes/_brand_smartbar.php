<?php

    if (isset($message)) {
        echo $message;
    }

	if (!isset($smartbar_selection)) {
		$smartbar_selection = 'brands';
	}

    $Smartbar = new PerchSmartbar($CurrentUser, $HTML, $Lang);

    $Smartbar->add_item([
        'active' => $smartbar_selection=='brands',
        'title' => $Lang->get('Brands'),
        'link'  => $API->app_nav('perch_shop_products'),
        'icon'  => 'ext/o-shop',
    ]);

    echo $Smartbar->render();