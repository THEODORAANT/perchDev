<?php

    echo $HTML->title_panel([
        'heading' => $Lang->get('Listing all webhooks'),
        'form'    => [
            'action' => $Form->action(),
            'button' => $Form->submit('btnSubmit', 'Sync', 'button button-icon icon-left', true, true, PerchUI::icon('ext/o-sync', 14))
        ]
    ], $CurrentUser);

    $Alert->output();
    echo $message;

    $Smartbar = new PerchSmartbar($CurrentUser, $HTML, $Lang);

    $Smartbar->add_item([
            'active' => true,
            'title'  => 'Webhooks',
            'link'   => $API->app_nav().'/webhooks/',
            'icon'   => 'blocks/cloud',
        ]);

    echo $Smartbar->render();


    $Listing = new PerchAdminListing($CurrentUser, $HTML, $Lang, $Paging);

    $Listing->add_col([
            'title'     => 'ID',
            'value'     => 'webhookMailChimpID',
            'sort'      => 'webhookMailChimpID',
        ]);

    $Listing->add_col([
            'title'     => 'URL',
            'value'     => 'webhookURL',
            'sort'      => 'webhookURL',
        ]);


    echo $Listing->render($webhooks);