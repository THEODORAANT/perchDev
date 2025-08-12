<?php

class PerchEvents_Booking extends PerchAPI_Base
{
	public $static_fields          = [];

	protected $table               = 'events_bookings';
	protected $pk                  = 'bookingID';
	public $singular_classname     = 'PerchEvents_Booking';
	protected $index_table         = '';
	private $bookingID               = false;

	public function __construct($api=false)
	{
		parent::__construct($api);

	}

	public function init($data)
    {
    		if ($this->bookingID && $this->bookingID!='') {
    			return $this->bookingID;
    		}

                if (!perch_member_logged_in() && PerchEvents_Session::is_set('bookingID') && PerchEvents_Session::get('bookingID')!='') {
                    $this->bookingID = PerchEvents_Session::get('bookingID');
                }else{
                     if (is_object($data)) {
                         $this->bookingID = $this->_create_new_booking($data);
                      }

                      if (!perch_member_logged_in()) {
                            PerchEvents_Session::set('bookingID', $this->bookingID);
                        }else{
                            PerchEvents_Session::delete('bookingID');
                      }

                }




    		return $this->bookingID;
    }

    public function update($data)
    {

    $this->init($data);
    $this->db->update($this->table, $data, 'bookingID', $this->bookingID);

    }
    public function update_from_form($data)
    {

         if(isset($data["status"]) and $data["status"]=="available"){
            $this->db->delete(PERCH_DB_PREFIX.'events_bookings', $this->pk, $this->id());
         }else{

           parent::update($data);


         }




    }

	private function _create_new_booking($data)
	{
		$member_id   = null;
        $customer_id = null;
		if (perch_member_logged_in()) {
			$member_id = perch_member_get('memberID');
			$Customers = new PerchEvents_Customers($this->api);
            $Customer = $Customers->find_from_logged_in_member();
            if ($Customer) {
               $customer_id =$Customer->id();

            }




        foreach($data->data as $key => $value) {

            if (strpos($key, 'submit') === 0) {
              $time=$value;
            }else if (strpos($key, 'eventID')===0){
                $eventID = $value;
            }else if (strpos($key, 'slotID')===0){
                $slotID = $value;
            }else if ($key=="dayID"){
               $dayID = $value;
             }else if (strpos($key, 'date') === 0){
                 $date = $value;
            }


        }

		return $this->db->insert($this->table, [
		    'customerID'=> $customer_id,
			'memberID'       => $member_id,
			'dayID'     => 	$dayID,
			'slotID'     => $slotID ,
			'eventID'    => $eventID,
			'time' => $time,
			'date' => $date
			]);
	   }
	    return false;
	}
	public function add_booking($eventID, $slotID, $dayID,$time,$memberID)
    	{
    	$data=array(   'memberID'       => $memberID,
                                                        'eventID' => $eventID,
                                                        'slotID' => $slotID,
                                                        'dayID' => $dayID,
                                                        'time' => $time);

                $this->db->update($this->table, $data, 'bookingID', $this->bookingID);

        }

     public function get_bookings_by_event($eventID){

             $bookings = $this->db->get_rows('SELECT * FROM '.$this->table.' WHERE eventID='.(int)$eventID);
             return  $bookings;

     }


    public function is_available($eventID,$slotID,$date,$time,$memberID){


             $sql='SELECT * FROM '.$this->table.' WHERE  eventID='.(int)$eventID.' AND slotID='.(int)$slotID.' AND `date`='.'"'.$date.'"'.'  AND time='.'"'.$time.'"';

             $booking = $this->db->get_row($sql);
            if(is_array($booking)) {
            if($booking["status"]=="available" or ($booking["memberID"]==$memberID and in_array($booking["status"],array("hold","confirmed") )) ){
                return true;
            }
            }
           return  false;

     }
     public function get_booking_status($eventID,$slotID,$date,$time){

             $booking = $this->db->get_row('SELECT * FROM '.$this->table.' WHERE  eventID='.(int)$eventID.' AND slotID='.(int)$slotID.' AND `date`='.'"'.$date.'"'.'  AND time='.'"'.$time.'"');
             $status="";
             if(isset($booking["status"])){
              $status=$booking["status"];
             }

             return  $status;

     }

	public function set_member($memberID)
	{
		$this->update(['memberID'=>$memberID]);
	}

	public function set_customer($customerID)
	{
		$this->customerID = $customerID;
		$this->update(['customerID'=>$customerID]);
	}



}


?>
