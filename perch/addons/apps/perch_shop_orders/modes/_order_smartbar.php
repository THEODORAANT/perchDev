<?php

    if (isset($message)) {
        echo $message;
    }


    if (!isset($smartbar_selection)) {
        $smartbar_selection = 'details';
    }

    if (is_object($Order)) {

        $Smartbar = new PerchSmartbar($CurrentUser, $HTML, $Lang);

        $Smartbar->add_item([
            'active' => $smartbar_selection=='details',
            'type'  => 'breadcrumb',
            'links' => [
                [
                    'title' => $Lang->get('Orders'),
                    'link'  => $API->app_nav(),
                ],
                [
                    'title' => $Order->orderInvoiceNumber(),
                    'link'  => $API->app_nav().'/order/?id='.$Order->id(),
                ],
            ]
        ]);

        $Smartbar->add_item([
            'active' => $smartbar_selection=='evidence',
            'title' => $Lang->get('Tax Evidence'),
            'link'  => $API->app_nav().'/order/evidence/?id='.$Order->id(),
            'icon'  => 'ext/o-museum',
        ]);
        echo $HTML->title_panel([
            'button'  => [
             'text' => 'Download PDF',

            'onclick'=>'exportInPDF()',
                'icon' => 'ext/o-cloud-download',
                ],
        ], $CurrentUser);

        echo $Smartbar->render();

    }

