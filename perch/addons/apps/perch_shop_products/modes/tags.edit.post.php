<?php
    echo $HTML->title_panel([
        'heading' => $Lang->get('Editing Tags for ‘%s’', $HTML->encode($Product->title())),
    ], $CurrentUser); 

    /* ----------------------------------------- SMART BAR ----------------------------------------- */

    $smartbar_selection = 'tags';
    include('_product_smartbar.php');

    /* ---------------------------------------- /SMART BAR ----------------------------------------- */


    $template_help_html = $Template->find_help();
    if ($template_help_html) {
        echo $HTML->heading2('Help');
        echo '<div class="template-help">' . $template_help_html . '</div>';
    }

    /* ---- FORM ---- */
    echo $Form->form_start('product-edit');

        echo $Form->fields_from_template($Template, $details);
        echo $Form->submit_field('btnSubmit', 'Save', $API->app_path());

    echo $Form->form_end();
    /* ---- /FORM ---- */
