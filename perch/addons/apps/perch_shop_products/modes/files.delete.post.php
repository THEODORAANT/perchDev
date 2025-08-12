<?php 

    # Side panel
    echo $HTML->side_panel_start();
    echo $HTML->heading3('Delete File');
    echo $HTML->para('You can delete a product file here.');
    echo $HTML->side_panel_end();
    
    
    # Main panel
    echo $HTML->main_panel_start(); 

    include('_subnav.php');

    echo $Form->form_start();
    
    if ($message) {
        echo $message;
    }else{
        echo $HTML->warning_message('Are you sure you wish to delete the file %s?', '<strong>'.$File->fileTitle().'</strong>');
        echo $Form->form_start();
		echo $Form->submit_field('btnSubmit', 'Delete', $API->app_path('perch_shop_products').'/product/files/?id='.$Product->id());
        echo $Form->form_end();
    }
    
    echo $HTML->main_panel_end();