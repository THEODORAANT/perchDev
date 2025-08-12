<?php

    if (is_object($List)) {
        $heading =  $Lang->get('Editing list ‘%s’', $HTML->encode($List->listTitle()));
    }else{
        $heading =  $Lang->get('Creating a new list');
    }

    echo $HTML->title_panel([
            'heading' => $heading,
        ], $CurrentUser);


    echo $message;


    $Smartbar = new PerchSmartbar($CurrentUser, $HTML, $Lang);

    $Smartbar->add_item([
            'active' => true,
            'title'  => 'Lists',
            'link'   => $API->app_nav(),
            'icon'   => 'blocks/mail',
        ]);

    echo $Smartbar->render();

    
    $template_help_html = $Template->find_help();
    if ($template_help_html) {
        echo $HTML->heading2('Help');
        echo '<div id="template-help">' . $template_help_html . '</div>';
    }
    
    echo $HTML->heading2('List options');    
    
    /* ---- FORM ---- */
    echo $Form->form_start('edit');

        echo $Form->fields_from_template($Template, $details);
        echo $Form->submit_field('btnSubmit', 'Save', $API->app_path());

    echo $Form->form_end();
    /* ---- /FORM ---- */
