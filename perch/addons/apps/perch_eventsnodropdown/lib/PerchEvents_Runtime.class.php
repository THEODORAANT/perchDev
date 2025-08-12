<?php

class PerchEvents_Runtime
{
	private static $instance;

	private $api                  = null;
	private $bookingID              = null;
	private $Booking                 = null;

	private $eventID           = null;
	private $slotID			  = null;
	


	public static function fetch()
	{
		if (!isset(self::$instance)) self::$instance = new PerchEvents_Runtime;
        return self::$instance;
	}

	public function __construct()
	{
		$this->api = new PerchAPI(1.0, 'perch_events');

	}

	public function init_booking($data=array())
	{
		if (!$this->bookingID) {
			$this->Booking = new PerchEvents_Booking($this->api);

			if($data){
			    $this->bookingID = $this->Booking->init($data);
			}

	    }
    }


	public function add_booking($SubmittedForm)
	{
	    $this->init_booking($SubmittedForm);

	}


	public function get_customer_id()
	{
		$memberID = perch_member_get('memberID');
		$Customer = $this->get_customer($memberID);
		return $Customer->id();
	}


	public function register_member_login($Event)
	{
		$this->init_booking();
		$memberID   = perch_member_get('memberID');

		$this->Booking->set_member($memberID);

		$Customers = new PerchEvents_Customers($this->api);
		$Customer = $Customers->find_by_memberID($memberID);

		if ($Customer) {
			$this->Booking->set_customer($Customer->id());

		}
	}



	public function register_customer_from_form($SubmittedForm)
	{
		$Session = PerchMembers_Session::fetch();

		$MembersForm = $SubmittedForm->duplicate(['first_name', 'last_name', 'email', 'password'], ['password']);

		$MembersForm->redispatched = true;
		$MembersForm->redispatch('perch_members');
		if ($Session->logged_in) {
			$Customers = new PerchEvents_Customers($this->api);
			$Customer = $Customers->create_from_form($SubmittedForm);
			if ($Customer) {
                $this->Booking->set_customer($Customer->id());

            }
		}

	}

	public function update_customer_from_form($SubmittedForm)
	{
		$Session = PerchMembers_Session::fetch();		

		if ($Session->logged_in) {

			$MembersForm = $SubmittedForm->duplicate(['first_name', 'last_name', 'email', 'token'], ['token']);
			$MembersForm->redispatch('perch_members');

			$Customers = new PerchEvents_Customers($this->api);
			$Customer = $Customers->find_from_logged_in_member();
			$Customer->update_from_form($SubmittedForm);
		}

	}

	public function get_customer_details()
	{
		$Customer = $this->get_customer();
		$out = $Customer->to_array();

		return $out;
	}


	private function get_customer($memberID=false)
	{
		if (!$memberID) $memberID = perch_member_get('id');

		$Customers = new PerchEvents_Customers($this->api);
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


	public function get_bookings($opts)
	{
	 $this->init_booking();

		$memberID = perch_member_get('memberID');
		$Customer = $this->get_customer($memberID);
		$db       = PerchDB::fetch();
		$Bookings   = new PerchEvents_Bookings($this->api);


		// Get the listing
		$r = $Bookings->get_filtered_listing($opts, function(PerchQuery $Query) use ($opts, $Customer, $db){

			$Query->where[] = ' customerID='.$db->pdb($Customer->id()).' ';

			// filter for a single
			if (isset($opts['bookingID'])) {
				// We do this here because standard filter functions convert numbers to floats, which
				// fails with overly large values. Sigh.
				$Query->where[] = ' bookingID='.$db->pdb($opts['bookingID']).' ';
			}

			return $Query;
		});

		return $r;
	}



}
