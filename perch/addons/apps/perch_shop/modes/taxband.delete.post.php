<?php
    # Side panel
    echo $HTML->side_panel_start();
    //echo $HTML->para('');
    echo $HTML->side_panel_end();
    
    # Main panel
    echo $HTML->main_panel_start(); 
    include('_subnav.php');
		
    echo $HTML->heading1('Deleting Brand ‘%s’', $HTML->encode($Brand->title()));  
    
    /* ---- FORM ---- */
    echo $Form->form_start('edit');

        echo $HTML->warning_message('Are you sure you wish to delete this brand?');
        echo $Form->submit_field('btnSubmit', 'Delete', $API->app_path());

    echo $Form->form_end();
    /* ---- /FORM ---- */
        
    echo $HTML->main_panel_end();
  