<?php
    if (!$CurrentUser->has_priv('perch_events.bookings.manage')) {
         PerchUtil::redirect($API->app_path());
    }

    $Bookings = new PerchEvents_Bookings($API);
    $Members = new PerchMembers_Members($API);
    $Form = $API->get('Form');

    $message = false;


    if (isset($_GET['id']) && $_GET['id']!='') {
        $bookingID = (int) $_GET['id'];
        $Booking = $Bookings->find($bookingID);
        $details = $Booking->to_array();
         $border =$Bookings->get_order_for_booking($bookingID);
          $details['order'] = $border->to_array();
      if(isset($details['memberID'])){
                $Member = $Members->find($details['memberID'] );
                $member_details = $Member->to_array();
                $details['memberName'] = $member_details["first_name"]." ".$member_details["last_name"];
                $details['memberID'] =  $member_details["memberID"];
                $details['memberEmail'] =  $member_details["memberEmail"];
      }


    }else{
        $bookingID = false;
        $Booking   = false;
        $details    = array();
    }


    $Template   = $API->get('Template');
    $Template->set('events/booking.html', 'events');

    $Form->handle_empty_block_generation($Template);

    $tags = $Template->find_all_tags_and_repeaters();


    $Form->set_required_fields_from_template($Template, $details);

    if ($Form->submitted()) {
		$postvars = array('bookingID','memberID','date_day','date_month','date_year','time_hour','time_minute','status');

    	$data = $Form->receive($postvars);

        $prev = false;

            if (isset($data['time_hour']) && isset($data['time_minute'])) {
                  $time = strtotime($data['time_hour'] . ':' . $data['time_minute'] . ':00');
                  $data['time']=date('H:i', $time);
                  unset($data['time_hour']);
                   unset($data['time_minute']);
                }
              if (isset($data['date_day']) && isset($data['date_month'])&& isset($data['date_year'])) {
                        $inputdate=$data['date_year'].'-'.$data['date_month'].'-'.$data['date_day'];
                        $date = strtotime($inputdate);
                        $data["date"]= date('Y-m-d', $date);
                        unset($data['date_day']);
                        unset($data['date_month']);
                        unset($data['date_year']);

                 }


        if (!is_object($Booking)) {

            $Booking = $Bookings->create($data);
            PerchUtil::redirect($API->app_path() .'/bookings/edit/?id='.$Booking->bookingID().'&created=1');
        }

        $available=false;

        if($Booking->is_available($Booking->eventID(),$Booking->slotID(),$data["date"],$data["time"],$data["memberID"])){

             $Booking->update_from_form($data);
             $available=true;
        }


        if (is_object($Booking)) {
                if($available){
                  $message = $HTML->success_message('Your Booking has been successfully edited. Return to Booking listing%s', '<a href="'.$API->app_path() .'/bookings">', '</a>');

                }else{
                     $message = $HTML->failure_message('Sorry, that booking is not available.');
                }
        }else{
            $message = $HTML->failure_message('Sorry, that booking could not be edited.');
        }




        $details = $Booking->to_array();
    }

    if (isset($_GET['created']) && !$message) {
        $message = $HTML->success_message('Your booking has been successfully created. Return to %scategory listing%s', '<a href="'.$API->app_path() .'/categories">', '</a>');
    }
