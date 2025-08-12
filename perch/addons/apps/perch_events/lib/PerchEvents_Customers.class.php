<?php

class PerchEvents_Customers extends PerchEvents_Factory
{
	public $api_method             = 'customers';
	public $api_list_method        = 'customers';
	public $singular_classname     = 'PerchEvents_Customer';
	public $static_fields          = ['customerFirstName', 'customerLastName', 'customerEmail', 'customerCreated', 'memberID'];
	public $remote_fields          = ['first_name', 'last_name', 'email', 'group'];

	protected $table               = 'events_customers';
	protected $pk                  = 'customerID';
	protected $index_table         = 'events_admin_index';
	protected $master_template	   = 'events/customers/customer.html';

	protected $default_sort_column = 'customerLastName';
	protected $created_date_column = 'customerCreated';

    protected $event_prefix = 'events.customer';


	public function find_by_memberID($memberID)
	{
		$sql = 'SELECT * FROM '.$this->table.' WHERE memberID='.$this->db->pdb((int)$memberID);
		return $this->return_instance($this->db->get_row($sql));
	}

	public function create_from_logged_in_member()
	{
		$Session = PerchMembers_Session::fetch();

		if ($Session->logged_in) {

			$fields = [
        				'first_name' => $Session->get('first_name'),
        				'last_name'  => $Session->get('last_name'),
        				'email'      => $Session->get('email'),
        			];

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

			$search_for = ['first_name', 'last_name', 'email' ];

            $Template   = $this->api->get('Template');
            $Template->set('events/customers/customer.html', 'events');
            $template_ids = $Template->find_all_tag_ids();

            $search_for = array_merge($search_for, $template_ids);

			foreach($search_for as $field) {
				if (isset($data[$field])) {
					$fields[$field] = $data[$field];
				}
			}
        	
        	$Customer =  $this->create([
        		'customerDynamicFields' => PerchUtil::json_safe_encode($fields),
        		'memberID'   => $Session->get('memberID'),
        	]);

        	if ($Customer) {


        		return $Customer;
        	}
        }

    	return false;
	}

	public function find_from_logged_in_member()
	{
		$Session = PerchMembers_Session::fetch();

		if ($Session->logged_in) {
            $Customer = $this->get_one_by('memberID', $Session->get('id'));

            if ($Customer) {
                return $Customer;
            }

			return $this->get_one_by('customerEmail', $Session->get('email'));
		}
	}

}
