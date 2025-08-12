<?php

class PerchTwillio_Customers extends PerchTwillio_Factory
{
	public $api_method             = 'customers';
	public $api_list_method        = 'customers';
	public $singular_classname     = 'PerchTwillio_Customer';
	public $static_fields          = ['customerFirstName', 'customerLastName', 'customerPhone', 'customerCreated','verified', 'memberID'];
	public $remote_fields          = [ 'phone', 'group'];

	protected $table               = 'twillio_customers';
	protected $pk                  = 'customerID';
	protected $index_table         = 'twillio_admin_index';
	protected $master_template	   = 'customers/customer.html';

	protected $default_sort_column = 'customerLastName';
	protected $created_date_column = 'customerCreated';

    protected $event_prefix = 'twillio.customer';


	public function find_by_memberID($memberID)
	{
		$sql = 'SELECT * FROM '.$this->table.' WHERE memberID='.$this->db->pdb((int)$memberID);
		return $this->return_instance($this->db->get_row($sql));
	}

	public function create_from_logged_in_member()
	{
		$Session = PerchMembers_Session::fetch();

		if ($Session->logged_in) {
  $rand_no = rand(100000, 999999);
			$fields = [
        				'first_name' => $Session->get('first_name'),
        				'last_name'  => $Session->get('last_name'),
        				'phone'      => $Session->get('phone'),
        				'verified'=>0,
        				'verified_code'=> $rand_no
        			];
            $this->send_verificaton_code_member($fields["phone"],$fields["verified_code"]);
        	return $this->create([
        		'customerDynamicFields' => PerchUtil::json_safe_encode($fields),
        		'memberID'   => $Session->get('memberID'),
        	]);

        }
	}

	public function create_from_form($SubmittedForm)
	{
		$data = $SubmittedForm->data;
		$Session = PerchMembers_Session::fetch();

		if ($Session->logged_in) {

			$fields = [];

			$search_for = ['first_name', 'last_name', 'phone' ];

            $Template   = $this->api->get('Template');
            $Template->set('customers/customer.html', 'twillio');
            $template_ids = $Template->find_all_tag_ids();

            $search_for = array_merge($search_for, $template_ids);

			foreach($search_for as $field) {
				if (isset($data[$field])) {
					$fields[$field] = $data[$field];
				}
			}
        	$fields["verified"] =0;
        	  $rand_no = rand(100000, 999999);
        	$fields["verified_code"] =$rand_no;
        	$Customer =  $this->create([
        		'customerDynamicFields' => PerchUtil::json_safe_encode($fields),
        		'memberID'   => $Session->get('memberID'),
        	]);

        	if ($Customer) {
            $this->send_verificaton_code_member($fields["phone"],$fields["verified_code"]);

        		return $Customer;
        	}
        }

    	return false;
	}
	public function send_verificaton_code_member($phonenumber,$code)
	{ 	$API  = new PerchAPI(1.0, 'perch_twillio');
            $codes=(string)$code;
	    $Messages = new PerchTwillio_Messages($API);
	   // echo "send_verificaton_code_member";
	   $first_character = mb_substr($phone, 0, 1);

               if($first_character!='+'){
               $phonenumber="+".$phonenumber;
               }
      $twillio_response=   $Messages->sendWithTwillio("Your verfication code is ".$codes,$phonenumber);
//print_r($twillio_response);

	}
	public function find_from_logged_in_member()
	{
		$Session = PerchMembers_Session::fetch();

		if ($Session->logged_in) {
            $Customer = $this->get_one_by('memberID', $Session->get('id'));

            if ($Customer) {
                return $Customer;
            }

			return $this->get_one_by('customerPhone', $Session->get('phone'));
		}
	}

}
