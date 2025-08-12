<?php ini_set('display_errors', '1');
      ini_set('display_startup_errors', '1');
      error_reporting(E_ALL);
    if (!$CurrentUser->has_priv('perch_twillio.dispatches.manage')) {
         PerchUtil::redirect($API->app_path());
    }
$title="";
    $Dispatches = new PerchTwillio_Dispatches($API);
    $Messages = new PerchTwillio_Messages($API);
        $Customers = new PerchTwillio_Customers($API);

    $Form = $API->get('Form');

    $message = false;
    $tocustomers= array();
     $tocustomersall =  $Customers->all();

 if (PerchUtil::count($tocustomersall)) {

                 foreach($tocustomersall as $PerchTwillio_Customer) {
                 $cus_details=json_decode($PerchTwillio_Customer->customerDynamicFields(), true);

                     $tocustomers[] = array('value'=>$PerchTwillio_Customer->customerID(), 'label'=>$cus_details["first_name"]." ".$cus_details["last_name"]." ".$cus_details["phone"]);
                 }

             }
 $sendto_messagesall = array();
 $sendto_messages =  $Messages->all();
  if (PerchUtil::count($sendto_messages)) {


                 foreach($sendto_messages as $PerchTwillio_Message) {
                     $sendto_messagesall[] = array('value'=>$PerchTwillio_Message->messageID(), 'label'=>$PerchTwillio_Message->messageText());
                 }

             }
    if (isset($_GET['id']) && $_GET['id']!='') {
        $dispatchID = (int) $_GET['id'];
        $Dispatch = $Dispatches->find($dispatchID);
        $details = $Dispatch->to_array();



    }else{
        $dispatchID = false;
        $Dispatch   = false;
        $details    = array();
    }

    $Template   = $API->get('Template');
    $Template->set('messages/dispatch.html', 'dispatches');

    $Form->handle_empty_block_generation($Template);

    $tags = $Template->find_all_tags_and_repeaters();


    $Form->set_required_fields_from_template($Template, $details);

    if ($Form->submitted()) {

		$postvars = array('messageID','status','tocustomers');

    	$data = $Form->receive($postvars);

        $prev = false;
         $data["dispatchDateTime"]= date('Y-m-d H:i');

         $tocustomers_arr= $data["tocustomers"];
     $data["tocustomers"]= json_encode( array('tocustomers' => $tocustomers_arr));


        if (!is_object($Dispatch)) {


           if($data["status"]=="send"){

            $Message = $Messages->find($data["messageID"]);
              $message_details = $Message->to_array();
$send_arr=array();
              foreach($tocustomers_arr as $cusid){

               $cus = $Customers->find($cusid);
               $cus_details=json_decode($cus->customerDynamicFields(), true);

             $twillio_response=   $Messages->sendWithTwillio($message_details["messageText"],$cus_details["phone"]);

             $send_arr[$cusid]=$twillio_response;

              }
$data["response"]=   json_encode($send_arr);
            }
              $Dispatch = $Dispatches->create($data);
         //  PerchUtil::redirect($API->app_path() .'/dispatches/edit/?id='.$Dispatch->dispatchID().'&created=1');
        }



        if (is_object($Dispatch)) {

                  $message = $HTML->success_message('Your Dispatch has been successfully edited. Return to Booking listing%s', '<a href="'.$API->app_path() .'/bookings">', '</a>');


        }else{
            $message = $HTML->failure_message('Sorry, that Dispatch could not be edited.');
        }




        $details = $Dispatch->to_array();
    }

    if (isset($_GET['created']) && !$message) {
        $message = $HTML->success_message('Your dispatch has been successfully created. Return to %scategory listing%s', '<a href="'.$API->app_path() .'/categories">', '</a>');
    }
