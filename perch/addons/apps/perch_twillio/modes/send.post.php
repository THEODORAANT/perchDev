<?php



    # Side panel
    echo $HTML->side_panel_start();
    echo $HTML->para('Delete an Message here.');

    echo $HTML->side_panel_end();


    # Main panel
    echo $HTML->main_panel_start();
    include('_subnav.php');

    echo $HTML->heading1('Send a Message');

    echo $Form->form_start();

    if ($message) {
        echo $message;
    }else{
        echo $HTML->warning_message('Are you sure you wish to delete the Message %s?', $details['messageID']);
        echo $Form->form_start();
        echo $Form->hidden('messageID', $details['messageID']);
		echo $Form->submit_field('btnSubmit', 'Delete', $API->app_path());


        echo $Form->form_end();
    }

    echo $HTML->main_panel_end();

?>
