<?php

    if (isset($message)) {
        echo $message;
    }


	if (!isset($smartbar_selection)) {
		$smartbar_selection = 'promos';
	}

    $Smartbar = new PerchSmartbar($CurrentUser, $HTML, $Lang);

    $Smartbar->add_item([
        'active' => $smartbar_selection=='promos',
        'title' => $Lang->get('Promotions'),
        'link'  => $API->app_nav('perch_shop').'/promos/',
        'icon'  => 'ext/o-scissors',
    ]);

    if (PERCH_RUNWAY) {
        $Smartbar->add_item([
            'active' => $smartbar_selection=='sales',
            'title' => $Lang->get('Sales'),
            'link'  => $API->app_nav('perch_shop').'/promos/sales/',
            'icon'  => 'ext/o-alarm',
        ]);
    }

    echo $Smartbar->render();