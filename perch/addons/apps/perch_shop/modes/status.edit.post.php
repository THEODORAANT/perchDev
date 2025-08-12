<?php

    if (is_object($Status)) {
        $title = $Lang->get('Editing Status ‘%s’', $HTML->encode($Status->statusTitle()));
    }else{
        $title = $Lang->get('Creating a New Order Status');
    }

    echo $HTML->title_panel([
        'heading' => $title,
    ], $CurrentUser);

    /* ----------------------------------------- SMART BAR ----------------------------------------- */
       include('_status_smartbar.php');
    /* ----------------------------------------- /SMART BAR ----------------------------------------- */

    $template_help_html = $Template->find_help();
    if ($template_help_html) {
        echo $HTML->heading2('Help');
        echo '<div class="template-help">' . $template_help_html . '</div>';
    }

    echo $HTML->heading2('Status');

    /* ---- FORM ---- */
    echo $Form->form_start('edit');


        if (is_object($Status)) {
            if (!$Status->statusEditable()) {
                //#$Form->display_only = true;
            }
        }

        echo $Form->fields_from_template($Template, $details);
        echo $Form->submit_field('btnSubmit', 'Save', $API->app_path());

    echo $Form->form_end();
