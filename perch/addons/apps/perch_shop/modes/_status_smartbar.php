<?php

    if (isset($message)) {
        echo $message;
    }

	if (!isset($smartbar_selection)) {
		$smartbar_selection = 'status';
	}

    $Smartbar = new PerchSmartbar($CurrentUser, $HTML, $Lang);

    $Smartbar->add_item([
        'active' => $smartbar_selection=='status',
        'title' => $Lang->get('Statuses'),
        'link'  => $API->app_nav('perch_shop').'/statuses/',
        'icon'  => 'ext/o-flag',
    ]);

    echo $Smartbar->render();