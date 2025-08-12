<?php
 
    echo $HTML->title_panel([
        'heading' => $heading1,
        ], $CurrentUser);
    
    if ($message) echo $message;    
    
    $template_help_html = $Template->find_help();
    if ($template_help_html) {
        echo $HTML->heading2('Help');
        echo '<div id="template-help">' . $template_help_html . '</div>';
    }
    
    echo $HTML->heading2('Event details');
    
    echo $Form->form_start();
    
        echo $Form->text_field('eventTitle', 'Name', isset($details['eventTitle'])?$details['eventTitle']:false);

		echo $Form->textarea_field('eventDescRaw', 'Description', isset($details['eventDescRaw'])?$details['eventDescRaw']:false, false, $Template->find_tag('eventDescHTML'));
		
		echo $Form->date_field('eventDateTime', 'Start Date', isset($details['eventDateTime'])?$details['eventDateTime']:false, true);

		echo $Form->date_field('eventEndDateTime', 'End Date', isset($details['eventEndDateTime'])?$details['eventEndDateTime']:false, true);
		
		echo $Form->fields_from_template($Template, $details, $Events->static_fields);
		
		$values = array();
        $opts = array();
        if(is_array($categories)) {
        	foreach($categories as $Category) {
        		$opts[] = array('label'=>$Category->categoryTitle(),'value'=>$Category->id());
        	}
        }

        echo $Form->checkbox_set('cat_ids', 'Categories', $opts, isset($details['cat_ids'])?$details['cat_ids']:array());


        		$values = array();
                $slot_opts = array();

                $slot_opts[] = array('label'=>"No Slots",'value'=>0);
                if(is_array($timeslots)) {
                	foreach($timeslots as $TimeSlot) {
                	$label="Slot Duration :".$TimeSlot["slot_duration"]." Start time :".$TimeSlot["startDate"]." - End time :".$TimeSlot["endDate"];
                		$slot_opts[] = array('label'=>$label,'value'=>$TimeSlot["slotID"]);
                	}
                }

        echo $Form->radio_set('slot','slots', 'Time Slots', $slot_opts, isset($details['slots'])?$details['slots']:array());
        echo $Form->submit_field('btnSubmit', 'Save', $API->app_path());
    
    echo $Form->form_end();
