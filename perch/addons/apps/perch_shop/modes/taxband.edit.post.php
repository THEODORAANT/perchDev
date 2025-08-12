<?php
    # Side panel
    echo $HTML->side_panel_start();
    //echo $HTML->para('');
    echo $HTML->side_panel_end();

    # Main panel
    echo $HTML->main_panel_start();
    include('_subnav.php');

    if (is_object($TaxBand)) {
        echo $HTML->heading1('Editing Tax Band ‘%s’', $HTML->encode($TaxBand->taxbandTitle()));
    }else{
        echo $HTML->heading1('Creating a New Tax Band');
    }

    $Alert->output();
    if ($message) echo $message;

    $template_help_html = $Template->find_help();
    if ($template_help_html) {
        echo $HTML->heading2('Help');
        echo '<div class="template-help">' . $template_help_html . '</div>';
    }

    echo $HTML->heading2('Tax Band');



    /* ---- FORM ---- */
    echo $Form->form_start('edit');

        echo $Form->fields_from_template($Template, $details);
        echo $Form->submit_field('btnSubmit', 'Save', $API->app_path());

    echo $Form->form_end();
    /* ---- /FORM ---- */

    echo $HTML->main_panel_end();
