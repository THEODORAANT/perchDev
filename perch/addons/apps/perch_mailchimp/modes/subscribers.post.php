<?php 
    echo $HTML->title_panel([
            'heading' => $Lang->get('Listing all subscribers'),
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
            'title'  => 'Subscribers',
            'link'   => $API->app_nav().'/subscribers/',
            'icon'   => 'core/users',
        ]);

    echo $Smartbar->render();


        $Listing = new PerchAdminListing($CurrentUser, $HTML, $Lang, $Paging);
    
        $Listing->add_col([
                'title'     => 'Email',
                'value'     => 'subscriberEmail',
                'sort'      => 'subscriberEmail',
                //'edit_link' => 'edit',
                'gravatar'  => 'subscriberEmail',
                'priv'      => 'perch_mailchimp.subscribers.edit',
            ]);

        $Listing->add_col([
                'title'     => 'First name',
                'value'     => 'subscriberFirstName',
                'sort'      => 'subscriberFirstName',
            ]);

        $Listing->add_col([
                'title'     => 'Last name',
                'value'     => 'subscriberLastName',
                'sort'      => 'subscriberLastName',
            ]);

        echo $Listing->render($subscribers);

