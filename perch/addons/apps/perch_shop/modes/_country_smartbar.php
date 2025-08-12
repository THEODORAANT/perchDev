<?php

    if (isset($message)) {
        echo $message;
    }

	if (!isset($smartbar_selection)) {
		$smartbar_selection = 'country';
	}

    $Smartbar = new PerchSmartbar($CurrentUser, $HTML, $Lang);

    $Smartbar->add_item([
        'active' => $smartbar_selection=='country',
        'title' => $Lang->get('Countries'),
        'link'  => $API->app_nav('perch_shop').'/countries/',
        'icon'  => 'core/o-world',
    ]);

    echo $Smartbar->render();