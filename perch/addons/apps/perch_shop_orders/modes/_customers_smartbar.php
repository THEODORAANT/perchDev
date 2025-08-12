<?php

    if (isset($message)) {
        echo $message;
    }

	if (!isset($smartbar_selection)) {
		$smartbar_selection = 'customers';
	}

    $Smartbar = new PerchSmartbar($CurrentUser, $HTML, $Lang);

    $Smartbar->add_item([
        'active' => $smartbar_selection=='customers',
        'title' => $Lang->get('Customers'),
        'link'  => $API->app_nav().'/customers/',
        'icon'  => 'core/users',
    ]);

    echo $Smartbar->render();