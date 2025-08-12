<?php 

    echo $HTML->title_panel([
        'heading' => $Lang->get('Exporting orders'),
    ], $CurrentUser);
   
	/* ----------------------------------------- SMART BAR ----------------------------------------- */

    $smartbar_selection = 'export';
    include('_orders_smartbar.php');
       
	/* ----------------------------------------- /SMART BAR ----------------------------------------- */

    echo $HTML->heading2('Export options');

    echo $Form->form_start();    
    echo $Form->fields_from_template($Template, array(), array(), false);
    echo $Form->submit_field('btnSubmit', 'Export', $API->app_path());
    echo $Form->form_end();
