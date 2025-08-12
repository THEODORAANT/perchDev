<?php

    if (isset($message)) {
        echo $message;
    }

	if (!isset($smartbar_selection)) {
		$smartbar_selection = 'options';
	}

    $Smartbar = new PerchSmartbar($CurrentUser, $HTML, $Lang);

    $Smartbar->add_item([
        'active' => $smartbar_selection=='options',
        'title' => $Lang->get('Options'),
        'link'  => $API->app_nav('perch_shop_products'),
        'icon'  => 'ext/o-ruler',
    ]);

    echo $Smartbar->render();