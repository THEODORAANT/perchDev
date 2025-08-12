<?php

        echo $HTML->title_panel([
            'heading' => $Lang->get('Listing all lists'),
            'form'    => [
                'action' => $Form->action(),
                'button' => $Form->submit('btnSubmit', 'Sync', 'button button-icon icon-left', true, true, PerchUI::icon('ext/o-sync', 14))
            ]
        ], $CurrentUser);

        $Smartbar = new PerchSmartbar($CurrentUser, $HTML, $Lang);

        $Smartbar->add_item([
                'active' => true,
                'title'  => 'Lists',
                'link'   => $API->app_nav(),
                'icon'   => 'blocks/mail',
            ]);

        echo $Smartbar->render();
    
        $Listing = new PerchAdminListing($CurrentUser, $HTML, $Lang, $Paging);
    
        $Listing->add_col([
                'title'     => 'Title',
                'value'     => 'listTitle',
                'sort'      => 'listTitle',
                'edit_link' => 'lists/edit',
            ]);

        $Listing->add_col([
                'title'     => 'List ID',
                'value'     => 'listMailChimpID',
                'sort'      => 'listMailChimpID',
            ]);

        $Listing->add_col([
                'title'     => 'Subscribers',
                'value'     => 'listMemberCount',
                'sort'      => 'listMemberCount',
            ]);

        $Listing->add_col([
                'title'     => 'Open rate',
                'sort'      => 'listOpenRate',
                'value'     => function($Item) {
                    return $Item->listOpenRate().'%';
                },
            ]);

        $Listing->add_col([
                'title'     => 'Click rate',
                'sort'      => 'listClickRate',
                'value'     => function($Item) {
                    return $Item->listClickRate().'%';
                },
            ]);
        
        // $Listing->add_delete_action([
        //         'priv'   => 'perch_mailchimp.lists.edit',
        //         'inline' => true,
        //         'path'   => 'lists/delete',
        //     ]);

        echo $Listing->render($lists);
