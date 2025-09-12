<?php

    if (isset($message)) {
        echo $message;
    }

	if (!isset($smartbar_selection)) {
		$smartbar_selection = 'products';
	}

    $Smartbar = new PerchSmartbar($CurrentUser, $HTML, $Lang);

    $Smartbar->add_item([
        'active' => $smartbar_selection=='products',
        'title' => $Lang->get('Products'),
        'link'  => $API->app_nav('perch_shop_products'),
        'icon'  => 'ext/o-shirt',
    ]);

    echo $Smartbar->render();