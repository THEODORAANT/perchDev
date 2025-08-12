<?php

		
    if (is_object($TaxLocation)) {
        $title = $Lang->get('Editing tax location ‘%s’', $HTML->encode($TaxLocation->locationTitle()));
    }else{
        $title = $Lang->get('Creating a new tax location');
    }

    echo $HTML->title_panel([
        'heading' => $title,
    ], $CurrentUser);

    /* ----------------------------------------- SMART BAR ----------------------------------------- */
        $smartbar_selection = 'locations';
        include('_tax_smartbar.php');
    /* ----------------------------------------- /SMART BAR ----------------------------------------- */

    
    $template_help_html = $Template->find_help();
    if ($template_help_html) {
        echo $HTML->heading2('Help');
        echo '<div class="template-help">' . $template_help_html . '</div>';
    }


    
    echo $HTML->heading2('Tax location');    
    
    /* ---- FORM ---- */
    echo $Form->form_start('edit');

        echo $Form->fields_from_template($Template, $details);
        echo $Form->submit_field('btnSubmit', 'Save', $API->app_path());

    echo $Form->form_end();
    /* ---- /FORM ---- */
        
