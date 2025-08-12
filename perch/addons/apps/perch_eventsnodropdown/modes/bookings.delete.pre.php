<?php

    if (!$CurrentUser->has_priv('perch_events.bookings.manage')) {
        exit;
    }

    $Bookings = new PerchEvents_Bookings($API);

    $Form = $API->get('Form');
    $Form->set_name('delete');

	$message = false;

	if (isset($_GET['id']) && $_GET['id']!='') {
	    $Booking = $Bookings->find($_GET['id']);
	}else{
	    PerchUtil::redirect($API->app_path());
	}


    if ($Form->submitted()) {

    	if (is_object($Booking)) {
    	    $Booking->delete();


            if ($Form->submitted_via_ajax) {
                echo $API->app_path().'/bookings/';
                exit;
            }else{
               PerchUtil::redirect($API->app_path().'/bookings/');
            }

        }else{
            $message = $HTML->failure_message('Sorry, that booking could not be deleted.');
        }
    }



    $details = $Booking->to_array();



