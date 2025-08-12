<?php

    if (is_object($Country)) {
        $title = $Lang->get('Editing country ‘%s’', $HTML->encode($Country->country()));
    }else{
        $title = $Lang->get('Creating a new country');
    }

    echo $HTML->title_panel([
        'heading' => $title,
    ], $CurrentUser);

        /* ----------------------------------------- SMART BAR ----------------------------------------- */
       include('_country_smartbar.php');
    /* ----------------------------------------- /SMART BAR ----------------------------------------- */

    $template_help_html = $Template->find_help();
    if ($template_help_html) {
        echo $HTML->heading2('Help');
        echo '<div class="template-help">' . $template_help_html . '</div>';
    }

    echo $HTML->heading2('Country');

    /* ---- FORM ---- */
    echo $Form->form_start('edit');

        echo $Form->fields_from_template($Template, $details);
        echo $Form->submit_field('btnSubmit', 'Save', $API->app_path());

    echo $Form->form_end();
    /* ---- /FORM ---- */
