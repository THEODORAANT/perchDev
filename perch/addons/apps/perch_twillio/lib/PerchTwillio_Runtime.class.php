<?php

class PerchTwillio_Runtime
{
	private static $instance;

	private $api                  = null;
	private $dispatchID              = null;
	private $Dispatch                 = null;

	private $messageID           = null;

	


	public static function fetch()
	{
		if (!isset(self::$instance)) self::$instance = new PerchTwillio_Runtime;
        return self::$instance;
	}

	public function __construct()
	{
		$this->api = new PerchAPI(1.0, 'perch_twillio');

	}




	public function get_customer_id()
	{
		$memberID = perch_member_get('memberID');
		$Customer = $this->get_customer($memberID);
		return $Customer->id();
	}
	public function is_customerphone_registered()
    	{ echo "is_customerphone_registered";
    		$Session = PerchMembers_Session::fetch();

        		if ($Session->logged_in) {
    		$memberID = $Session->get('memberID');//perch_member_get('memberID');
    		$Customer = $this->get_customer($memberID);
    		echo "Customer";
    		if($Customer){
    		return true;
    		}
    		return false;
    		}
    		return false;
    	}

	public function is_customer_verified()
	{
		$Session = PerchMembers_Session::fetch();

    		if ($Session->logged_in) {
		$memberID = $Session->get('memberID');//perch_member_get('memberID');
		$Customer = $this->get_customer($memberID);

		return $Customer->verified();
		}
		return false;
	}
	public function register_member_login($Event)
	{ echo "register_member_login";
	//	$this->init_booking();
		$memberID   = perch_member_get('memberID');

	//	$this->Booking->set_member($memberID);

		$Customers = new PerchTwillio_Customers($this->api);
		$Customer = $Customers->find_by_memberID($memberID);

	/*	if ($Customer) {
			$this->Booking->set_customer($Customer->id());

		}*/
	}



	public function register_customer_from_form($SubmittedForm)
	{
		$Session = PerchMembers_Session::fetch();
print_r($Session);
		$MembersForm = $SubmittedForm->duplicate(['first_name', 'last_name', 'phone','verified','password'], ['password']);

		$MembersForm->redispatched = true;
		$MembersForm->redispatch('perch_members');

		if ($Session->logged_in) {

			$Customers = new PerchTwillio_Customers($this->api);
			$Customer = $Customers->create_from_form($SubmittedForm);
			/*if ($Customer) {
                $this->Booking->set_customer($Customer->id());

            }*/
		}

	}

	public function update_customer_from_form($SubmittedForm)
	{
		$Session = PerchMembers_Session::fetch();		
 if ( $SubmittedForm->validate()) {
		if ($Session->logged_in) {

			$MembersForm = $SubmittedForm->duplicate(['first_name', 'last_name', 'phone', 'token'], ['token']);
			$MembersForm->redispatch('perch_members');

			$Customers = new PerchTwillio_Customers($this->api);
			$Customer = $Customers->find_from_logged_in_member();

			$SubmittedForm->data["verified"] =0;
                    	  $rand_no = rand(100000, 999999);
             $SubmittedForm->data["verified_code"] =$rand_no;
			$Customer->update_from_form($SubmittedForm);
			//$Customer->update($data);
$data = $SubmittedForm->data;
			$Customers->send_verificaton_code_member($data["phone"],$data["verified_code"]);

			if(isset($SubmittedForm->data["return_url"])){
			   PerchUtil::redirect($SubmittedForm->data['return_url']);
			}

		}
}
	}

	public function get_customer_details()
	{
		$Customer = $this->get_customer();
		$out = $Customer->to_array();

		return $out;
	}

	public function verified_customer($SubmittedForm)
	{
	 $data = $SubmittedForm->data;
	 //print_r($data);
		$Customers = new PerchTwillio_Customers($this->api);
    			$Customer = $Customers->find_from_logged_in_member();
// print_r($Customer);

    		if ($Customer) {
	               $details = $Customer->to_array();

	             //  echo  $details ['verified_code']; echo $data['verify_code'];
	               if( $details ['verified_code']==$data['verify_code']){
	             //  $details['verified']=1;
	             $customerFields=PerchUtil::json_safe_decode($details["customerDynamicFields"]);

	                  //echo "update verify";
                  			$SubmittedForm->data["verified"] =1;

                  			$Customer->update_from_form($SubmittedForm);

                    return true;
                    }
    				return false;



    		}

    		return false;
	}
	private function get_customer($memberID=false)
	{
		if (!$memberID) $memberID = perch_member_get('id');

		$Customers = new PerchTwillio_Customers($this->api);
		$Customer = $Customers->find_by_memberID($memberID);

		if (!$Customer) {

			// does customer exist against another Member? (e.g. for anon login)
			$Customer = $Customers->find_from_logged_in_member();

			if ($Customer) {
				$Customer->update_locally(['memberID'=>$memberID]);


				return $Customer;
			}

			$Customer = $Customers->create_from_logged_in_member();
		}

		return $Customer;
	}






}
