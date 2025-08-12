<?php 
    echo $HTML->title_panel([
        'heading' => $Lang->get('Importing Episodes'),
    ], $CurrentUser);


    $Smartbar = new PerchSmartbar($CurrentUser, $HTML, $Lang);

    $Smartbar->add_item([
        'active' => false,
        'type'  => 'breadcrumb',
        'links' => [
            [
                'title' => $Lang->get('Shows'),
                'link'  => $API->app_nav(),
            ],
            [
                'title' => $Show->showTitle(),
                'link'  => $API->app_nav().'/show/?id='.$Show->id(),
            ]
        ]
    ]);

    $Smartbar->add_item([
        'active' => false,
        'title' => $Lang->get('Options'),
        'link'  => $API->app_nav().'/edit/?id='.$Show->id(),
        'icon'  => 'core/o-toggles',
    ]);

    $Smartbar->add_item([
        'active' => true,
        'title' => $Lang->get('Import'),
        'link'  => $API->app_nav().'/show/import/?id='.$Show->id(),
        'icon'  => 'core/inbox-download',
        'position' => 'end'
    ]);


    echo $Smartbar->render();


    echo $HTML->heading2('Import');

    if (!$importing) {

        echo $Form->form_start('import', 'magnetic-save-bar');

        echo $Form->text_field('url', 'RSS URL', '', false, false, ' placeholder="https://" ');


        echo $Form->submit_field('btnSubmit', 'Import', $API->app_path());


        echo $Form->form_end();


    }
