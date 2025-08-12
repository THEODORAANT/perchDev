<?php

    echo $HTML->title_panel([
        'heading' => $Lang->get('Listing all emails'),
        'button'  => [
            'text' => $Lang->get('Add email'),
            'link' => $API->app_nav().'/emails/edit/',
            'icon' => 'core/plus',
            'priv' => 'perch_shop.email.create',
        ],
    ], $CurrentUser);

    /* ----------------------------------------- SMART BAR ----------------------------------------- */
       include('_email_smartbar.php');
    /* ----------------------------------------- /SMART BAR ----------------------------------------- */

    $Listing = new PerchAdminListing($CurrentUser, $HTML, $Lang, $Paging);
    $Listing->add_col([
            'title'     => 'Name',
            'value'     => 'emailTitle',
            'sort'      => 'emailTitle',
            'edit_link' => 'edit',
            'priv'      => 'perch_shop.emails.edit',
        ]);

    $Listing->add_col([
            'title'     => 'Subject',
            'value'     => 'subject',
            'sort'      => 'subject',
        ]);
    
    $Listing->add_delete_action([
            'priv'   => 'perch_shop.emails.delete',
            'inline' => true,
            'path'   => 'delete',
        ]);

    echo $Listing->render($emails);
