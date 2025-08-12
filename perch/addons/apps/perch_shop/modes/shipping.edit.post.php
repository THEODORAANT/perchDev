<?php
		
    if (is_object($Shipping)) {
        $title = $Lang->get('Editing Shipping Method ‘%s’', $HTML->encode($Shipping->shippingTitle()));
    }else{
        $title = $Lang->get('Creating a New Shipping Method');
    }


    echo $HTML->title_panel([
        'heading' => $title,
    ], $CurrentUser);





    /* ----------------------------------------- SMART BAR ----------------------------------------- */
       include('_shipping_smartbar.php');
    /* ----------------------------------------- /SMART BAR ----------------------------------------- */ 
    
    $template_help_html = $Template->find_help();
    if ($template_help_html) {
        echo $HTML->heading2('Help');
        echo '<div class="template-help">' . $template_help_html . '</div>';
    }
    
    echo $HTML->heading2('Shipping method');    
    
    /* ---- FORM ---- */
    echo $Form->form_start('edit');

        echo $Form->fields_from_template($Template, $details);
        echo $Form->submit_field('btnSubmit', 'Save', $API->app_path());

    echo $Form->form_end();
    /* ---- /FORM ---- */
        
