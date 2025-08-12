<?php
    if (is_object($Product)) {
        if ($Product->is_variant()) {
            $title = $Lang->get('Editing product variant ‘%s’', $HTML->encode($Product->title() .': '.$Product->productVariantDesc()));
        }else{
            $title = $Lang->get('Editing product ‘%s’', $HTML->encode($Product->title()));
        }
        
    }else{
        $title = $Lang->get('Creating a new product');
    }

    echo $HTML->title_panel([
        'heading' => $title,
    ], $CurrentUser);

    /* ----------------------------------------- SMART BAR ----------------------------------------- */

    if (is_object($Product) && $Product->is_variant()) {
        $smartbar_selection = 'variants';
    }

    include('_product_smartbar.php');

    /* ---------------------------------------- /SMART BAR ----------------------------------------- */


    
    $template_help_html = $Template->find_help();
    if ($template_help_html) {
        echo $HTML->heading2('Help');
        echo '<div class="template-help">' . $template_help_html . '</div>';
    }

    echo $HTML->heading2('Product');    
    
    /* ---- FORM ---- */
    echo $Form->form_start('product-edit');

        echo $Form->fields_from_template($Template, $details);
        echo $Form->submit_field('btnSubmit', 'Save', $API->app_path());

    echo $Form->form_end();
    /* ---- /FORM ---- */
