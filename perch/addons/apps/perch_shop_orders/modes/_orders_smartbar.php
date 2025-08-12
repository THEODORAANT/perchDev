<?php

    if (isset($message)) {
        echo $message;
    }


    if (!isset($smartbar_selection)) {
        $smartbar_selection = 'list';
    }


        $Smartbar = new PerchSmartbar($CurrentUser, $HTML, $Lang);

        $Smartbar->add_item([
            'active' => $smartbar_selection=='list',
            'title' => $Lang->get('Orders'),
            'link'  => $API->app_nav().'/',
            'icon'  => 'ext/o-truck',
        ]);

        $Smartbar->add_item([
            'active' => $smartbar_selection=='export',
            'title' => $Lang->get('Export'),
            'link'  => $API->app_nav().'/export/',
            'icon'  => 'ext/o-cloud-download',
            'position' => 'end',
        ]);


        echo $Smartbar->render();
