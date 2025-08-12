<?php	
    if (is_object($Sale)) {
        $title = $Lang->get('Editing sale ‘%s’', $HTML->encode($Sale->saleTitle()));
    }else{
        $title = $Lang->get('Creating a new sale');
    }

    echo $HTML->title_panel([
        'heading' => $title,
    ], $CurrentUser);

    /* ----------------------------------------- SMART BAR ----------------------------------------- */
        $smartbar_selection = 'sales';
        include('_promo_smartbar.php');
    /* ----------------------------------------- /SMART BAR ----------------------------------------- */

    $template_help_html = $Template->find_help();
    if ($template_help_html) {
        echo $HTML->heading2('Help');
        echo '<div class="template-help">' . $template_help_html . '</div>';
    }
    
    echo $HTML->heading2('Sale');    
    
    /* ---- FORM ---- */
    echo $Form->form_start('edit');

        echo $Form->fields_from_template($Template, $details);
        echo $Form->submit_field('btnSubmit', 'Save', $API->app_path());

    echo $Form->form_end();
    /* ---- /FORM ---- */