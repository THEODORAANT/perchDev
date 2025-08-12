<?php

    echo $HTML->title_panel([
        'heading' => $Lang->get($heading1),
    ], $CurrentUser);

    if ($message) echo $message;

    echo $HTML->heading2('Member documents');

    echo $Form->form_start(false);

        //echo $Form->text_field('memberEmail', 'Email', isset($details['memberEmail'])?$details['memberEmail']:false, 'l');


        echo $Form->fields_from_template($Template, $details, array(), false);


        ?>
