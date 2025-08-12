<?php	
    if (is_object($Promotion)) {
        $title = $Lang->get('Editing Promotion ‘%s’', $HTML->encode($Promotion->promoTitle()));
    }else{
        $title = $Lang->get('Creating a New Promotion');
    }

    echo $HTML->title_panel([
        'heading' => $title,
    ], $CurrentUser);

    /* ----------------------------------------- SMART BAR ----------------------------------------- */
       include('_promo_smartbar.php');
    /* ----------------------------------------- /SMART BAR ----------------------------------------- */

    $template_help_html = $Template->find_help();
    if ($template_help_html) {
        echo $HTML->heading2('Help');
        echo '<div class="template-help">' . $template_help_html . '</div>';
    }
    
    echo $HTML->heading2('Promotion');    
    
    /* ---- FORM ---- */
    echo $Form->form_start('edit');

        echo $Form->fields_from_template($Template, $details);
        echo $Form->submit_field('btnSubmit', 'Save', $API->app_path());

    echo $Form->form_end();
    /* ---- /FORM ---- */
        
    echo $HTML->main_panel_end();
  