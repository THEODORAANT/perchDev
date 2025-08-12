<?php

    if (is_object($Zone)) {
        $title = $Lang->get('Editing Shipping Zone ‘%s’', $HTML->encode($Zone->zoneTitle()));
    }else{
        $title = $Lang->get('Creating a New Shipping Zone');
    }

    echo $HTML->title_panel([
        'heading' => $title,
    ], $CurrentUser);

    /* ----------------------------------------- SMART BAR ----------------------------------------- */
       $smartbar_selection = 'zones';
       include('_shipping_smartbar.php');
    /* ----------------------------------------- /SMART BAR ----------------------------------------- */
    
    $template_help_html = $Template->find_help();
    if ($template_help_html) {
        echo $HTML->heading2('Help');
        echo '<div class="template-help">' . $template_help_html . '</div>';
    }
    
    echo $HTML->heading2('Shipping zone');    
    
    /* ---- FORM ---- */
    echo $Form->form_start('edit');

        echo $Form->fields_from_template($Template, $details);
        echo $Form->submit_field('btnSubmit', 'Save', $API->app_path());

    echo $Form->form_end();
    /* ---- /FORM ---- */
        
