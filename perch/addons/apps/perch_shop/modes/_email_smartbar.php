<?php

    if (isset($message)) {
        echo $message;
    }

	if (!isset($smartbar_selection)) {
		$smartbar_selection = 'email';
	}

    $Smartbar = new PerchSmartbar($CurrentUser, $HTML, $Lang);

    $Smartbar->add_item([
        'active' => $smartbar_selection=='email',
        'title' => $Lang->get('Emails'),
        'link'  => $API->app_nav('perch_shop').'/emails/',
        'icon'  => 'ext/o-mail',
    ]);

    echo $Smartbar->render();