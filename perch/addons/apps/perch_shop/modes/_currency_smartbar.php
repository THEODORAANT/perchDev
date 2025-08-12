<?php

    if (isset($message)) {
        echo $message;
    }

	if (!isset($smartbar_selection)) {
		$smartbar_selection = 'currency';
	}

    $Smartbar = new PerchSmartbar($CurrentUser, $HTML, $Lang);

    $Smartbar->add_item([
        'active' => $smartbar_selection=='currency',
        'title' => $Lang->get('Currencies'),
        'link'  => $API->app_nav('perch_shop').'/currencies/',
        'icon'  => 'ext/o-money',
    ]);

    echo $Smartbar->render();