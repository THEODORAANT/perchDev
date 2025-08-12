<?php

    if (is_object($TimeSlot)) {
        $title = $Lang->get('Editing ‘%s’ Time Slot', $HTML->encode($details['slotID']));
    }else{
        $title = $Lang->get('Creating a Time Slot');
    }
    echo $HTML->title_panel([
            'heading' => $title,
        ], $CurrentUser);

    if ($message) echo $message;


    $Smartbar = new PerchSmartbar($CurrentUser, $HTML, $Lang);

    $Smartbar->add_item([
        'active' => true,
        'type'  => 'breadcrumb',
        'links' => [
            [
                'title' => $Lang->get('Time Slots'),
                'link'  => $API->app_nav().'/timeslots/',
            ],
            [
                'title' => (is_object($TimeSlot) ? $TimeSlot->slotID() : $Lang->get('New Time Slot')),
                'link'  => $API->app_nav().'/timeslots/edit/'.(is_object($TimeSlot) ? '?id='.$TimeSlot->slotID() : ''),
            ],
        ]
    ]);

    echo $Smartbar->render();


    $template_help_html = $Template->find_help();
    if ($template_help_html) {
        echo $HTML->heading2('Help');
        echo '<div id="template-help">' . $template_help_html . '</div>';
    }


    echo $HTML->heading2('Time Slot details');


    echo $Form->form_start();

        echo $Form->text_field('slot_duration', 'Slot Duration', (isset($details['slot_duration']) ? $details['slot_duration'] : ''));
        echo $Form->time_field('startDate', 'Start Date', isset($details['startDate'])?$details['startDate']:false, true);
        echo $Form->time_field('endDate', 'End Date', isset($details['endDate'])?$details['endDate']:false, true);

		$values = array();
        $opts = array();
        if(is_array($days)) {
        	foreach($days as $day) {

        		$opts[] = array('label'=>$day["day"],'value'=>$day["dayID"]);
        	}
        }

        echo $Form->checkbox_set('day_ids', 'Days', $opts, isset($details['day_ids'])?$details['day_ids']:array());
        echo $Form->hidden('slotID', (isset($details['slotID']) ? $details['slotID'] : ''));



        echo $Form->fields_from_template($Template, $details, $TimeSlots->static_fields);


        echo $Form->submit_field('btnSubmit', 'Save', $API->app_path().'/timeslots/');


    echo $Form->form_end();
