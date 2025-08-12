<?php

    if (is_object($Brand)) {
        $title = $Lang->get('Editing Brand ‘%s’', $HTML->encode($Brand->brandTitle()));
    }else{
        $title = $Lang->get('Creating a New Brand');
    }

    echo $HTML->title_panel([
        'heading' => $title,
    ], $CurrentUser); 

    /* ----------------------------------------- SMART BAR ----------------------------------------- */
       include('_brand_smartbar.php');
    /* ----------------------------------------- /SMART BAR ----------------------------------------- */

    
    $template_help_html = $Template->find_help();
    if ($template_help_html) {
        echo $HTML->heading2('Help');
        echo '<div class="template-help">' . $template_help_html . '</div>';
    }
    
    echo $HTML->heading2('Brand');    
    
    /* ---- FORM ---- */
    echo $Form->form_start('edit');

        echo $Form->fields_from_template($Template, $details);
        echo $Form->submit_field('btnSubmit', 'Save', $API->app_path());

    echo $Form->form_end();
    /* ---- /FORM ---- */
        
    echo $HTML->main_panel_end();
  