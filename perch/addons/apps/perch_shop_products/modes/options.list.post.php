<?php
    
        echo $HTML->title_panel([
        'heading' => $Lang->get('Listing all options'),
        'button'  => [
            'text' => $Lang->get('Add option'),
            'link' => $API->app_nav().'/options/edit/',
            'icon' => 'core/plus',
            'priv' => 'perch_shop.options.create',
        ],
    ], $CurrentUser);

    /* ----------------------------------------- SMART BAR ----------------------------------------- */
        include('_options_smartbar.php');
    /* ----------------------------------------- /SMART BAR ----------------------------------------- */

    $Listing = new PerchAdminListing($CurrentUser, $HTML, $Lang, $Paging);
    $Listing->add_col([
            'title'     => 'Title',
            'value'     => 'title',
            'sort'      => 'title',
            'edit_link' => 'edit',
            'priv'      => 'perch_shop.options.edit',
        ]);

    $Listing->add_col([
            'title'     => 'Precendence',
            'value'     => 'optionPrecendence',
            'sort'      => 'optionPrecendence',
        ]);    

    $Listing->add_delete_action([
            'priv'   => 'perch_shop.options.delete',
            'inline' => true,
            'path'   => 'delete',
        ]);

    echo $Listing->render($options);