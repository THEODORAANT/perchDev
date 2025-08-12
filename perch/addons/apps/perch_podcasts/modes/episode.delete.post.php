<?php

    echo $HTML->title_panel([
        'heading' => $Lang->get('Delete an Episode'),
    ], $CurrentUser);
    

    
    if ($message) {
        echo $message;
    }else{
        echo $HTML->warning_message('Are you sure you wish to delete the episode %s?', $details['episodeTitle']);
        echo $Form->form_start();
		echo $Form->submit_field('btnSubmit', 'Delete', $API->app_path());


        echo $Form->form_end();
    }
