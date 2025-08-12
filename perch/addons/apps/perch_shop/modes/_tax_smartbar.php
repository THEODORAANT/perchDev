<?php

    if (isset($message)) {
        echo $message;
    }

	if (!isset($smartbar_selection)) {
		$smartbar_selection = 'groups';
	}


    $Smartbar = new PerchSmartbar($CurrentUser, $HTML, $Lang);

    $Smartbar->add_item([
        'active' => $smartbar_selection=='groups',
        'title' => $Lang->get('Groups'),
        'link'  => $API->app_nav('perch_shop').'/tax/',
        'icon'  => 'ext/o-museum',
    ]);

    $Smartbar->add_item([
        'active' => $smartbar_selection=='locations',
        'title' => $Lang->get('Locations'),
        'link'  => $API->app_nav('perch_shop').'/tax/locations/',
        'icon'  => 'ext/o-map',
    ]);


    echo $Smartbar->render();

