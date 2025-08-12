<?php

    if (isset($message)) {
        echo $message;
    }


	if (!isset($smartbar_selection)) {
		$smartbar_selection = 'shippings';
	}

    $Smartbar = new PerchSmartbar($CurrentUser, $HTML, $Lang);

    $Smartbar->add_item([
        'active' => $smartbar_selection=='shippings',
        'title' => $Lang->get('Methods'),
        'link'  => $API->app_nav('perch_shop').'/shippings/',
        'icon'  => 'ext/o-truck',
    ]);

    $Smartbar->add_item([
        'active' => $smartbar_selection=='zones',
        'title' => $Lang->get('Zones'),
        'link'  => $API->app_nav('perch_shop').'/shippings/zones/',
        'icon'  => 'ext/o-map',
    ]);


    echo $Smartbar->render();