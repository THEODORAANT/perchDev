<?php

		
    if (is_object($File)) {
        $title = $Lang->get('Editing file ‘%s’', $HTML->encode($File->fileTitle()));
    }else{
        $title = $Lang->get('Adding a new file');
    }

    echo $HTML->title_panel([
        'heading' => $title,
    ], $CurrentUser); 

    /* ----------------------------------------- SMART BAR ----------------------------------------- */

    $smartbar_selection = 'files';
    include('_product_smartbar.php');

    /* ---------------------------------------- /SMART BAR ----------------------------------------- */

    
    $template_help_html = $Template->find_help();
    if ($template_help_html) {
        echo $HTML->heading2('Help');
        echo '<div class="template-help">' . $template_help_html . '</div>';
    }
    
    echo $HTML->heading2('File details');    
    
    /* ---- FORM ---- */
    echo $Form->form_start('edit');

        echo $Form->fields_from_template($Template, $details);
        echo $Form->submit_field('btnSubmit', 'Save', $API->app_path());

    echo $Form->form_end();
    /* ---- /FORM ---- */
        