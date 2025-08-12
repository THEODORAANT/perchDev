<?php

    if (is_object($Option)) {
        $title = $Lang->get('Editing Option ‘%s’', $HTML->encode($Option->title()));
    }else{
        $title = $Lang->get('Creating a New Option');
    }


    echo $HTML->title_panel([
            'heading' => $title,
        ], $CurrentUser);



    $template_help_html = $Template->find_help();
    if ($template_help_html) {
        echo $HTML->heading2('Help');
        echo '<div class="template-help">' . $template_help_html . '</div>';
    }


    echo $HTML->heading2('Option');

    /* ---- FORM ---- */
    echo $Form->form_start('product-edit');

        echo $Form->fields_from_template($Template, $details);
        echo $Form->submit_field('btnSubmit', 'Save', $API->app_path());

    echo $Form->form_end();
    /* ---- /FORM ---- */

    echo $HTML->main_panel_end();
