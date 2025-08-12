<?php
    if (!$CurrentUser->has_priv('perch_events.timeslots.manage')) {
         PerchUtil::redirect($API->app_path());
    }

    $TimeSlots = new PerchEvents_TimeSlots($API);
    $days=   $TimeSlots->get_days();

    $Form = $API->get('Form');

    $message = false;


    if (isset($_GET['id']) && $_GET['id']!='') {
        $slotID = (int) $_GET['id'];
        $TimeSlot = $TimeSlots->find($slotID);
        $details = $TimeSlot->to_array();
    }else{
        $slotID = false;
        $TimeSlot   = false;
        $details    = array();
    }


    $Template   = $API->get('Template');
    $Template->set('events/timeslot.html', 'events');

    $Form->handle_empty_block_generation($Template);

    $tags = $Template->find_all_tags_and_repeaters();


    $Form->require_field('slot_duration', 'Required');
    $Form->set_required_fields_from_template($Template, $details);

    if ($Form->submitted()) {

		$postvars = array('slot_duration','startDate_hour','startDate_minute','endDate_hour','endDate_minute','endDate','day_ids');

    	$data = $Form->receive($postvars);

        $prev = false;


        if (!is_object($TimeSlot)) {

            $TimeSlot = $TimeSlots->create($data);
            PerchUtil::redirect($API->app_path() .'/timeslots/edit/?id='.$TimeSlot->slotID().'&created=1');
        }

        $TimeSlot->update($data);

        if (is_object($TimeSlot)) {
            $message = $HTML->success_message('Your time slot has been successfully edited. Return to %stimeslot%s', '<a href="'.$API->app_path() .'/timeslots">', '</a>');
        }else{
            $message = $HTML->failure_message('Sorry, that time slot  could not be edited.');
        }


        // clear the caches
        PerchEvents_Cache::expire_all();

        $details = $TimeSlot->to_array();
    }

    if (isset($_GET['created']) && !$message) {
        $message = $HTML->success_message('Your category has been successfully created. Return to %stimeslot%s', '<a href="'.$API->app_path() .'/timeslots">', '</a>');
    }
