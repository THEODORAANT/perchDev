<?php

class PerchShop_Addresses extends PerchShop_Factory
{
	public $api_method             = 'addresses';
	public $api_list_method        = 'addresses';
	public $singular_classname     = 'PerchShop_Address';
	public $static_fields          = ['addressTitle', 'addressFirstName', 'addressLastName', 'addressCompany', 'addressLine1', 'addressCreated', 'customerID', 'countryID'];
	public $remote_fields          = ['first_name', 'last_name', 'address_1', 'address_2', 'postcode', 'country', 'company', 'city', 'customer', 'phone', 'county', 'instructions'];
	
	protected $table               = 'shop_addresses';
	protected $pk                  = 'addressID';
	#protected $index_table         = 'shop_admin_index';
	protected $master_template	   = 'shop/addresses/address.html';
	
	protected $default_sort_column = 'addressTitle';
	protected $created_date_column = 'addressCreated';
	protected $deleted_date_column = 'addressDeleted';

	protected $event_prefix = 'shop.address';

	private $customerID = false;

	protected $runtime_restrictions = [
		[
			'field'          => 'orderID',
			'values'         => [''],
			'negative_match' => false,
			'match'          => 'all',
			'fuzzy'			 => false,
			'defeatable'	 => true,
		]
	];


	public function find_for_customer($customerID, $type_slug)
	{
		$sql = 'SELECT * FROM '.$this->table.' WHERE customerID='.$this->db->pdb($customerID).' AND orderID IS NULL AND countryID>0 AND addressSlug='.$this->db->pdb($type_slug);
		return $this->return_instance($this->db->get_row($sql));
	}

	public function get_for_customer($customerID)
	{
		$sql = 'SELECT * FROM '.$this->table.' WHERE customerID='.$this->db->pdb($customerID).' AND addressDeleted IS NULL AND orderID IS NULL';

		return $this->return_instances($this->db->get_rows($sql));
	}

	public function find_for_customer_by_id($customerID, $id)
	{
		$sql = 'SELECT * FROM '.$this->table.' WHERE customerID='.$this->db->pdb($customerID).' AND addressID='.$this->db->pdb((int)$id);
		return $this->return_instance($this->db->get_row($sql));
	}

	public function create_from_logged_in_member($customerID, $type_slug='default')
	{
		$Session = PerchMembers_Session::fetch();

		if ($Session->logged_in) {

			$fields = [];

			foreach($this->remote_fields as $field) {
				$fields[$field] = $Session->get($field);
			}
        				
        	$fields['customer'] = $customerID;


       	
        	return $this->create([
        		'addressDynamicFields' => PerchUtil::json_safe_encode($fields),
        		'addressTitle'   => $type_slug,
        		'addressSlug'    => $type_slug,
        	]);
        	       	
        }
	}

	public function create_from_default($customerID, $type_slug)
	{
		$sql = 'SELECT * FROM '.$this->table.' WHERE customerID='.$this->db->pdb($customerID).' AND orderID IS NULL AND countryID>0 AND addressSlug=\'default\'';
		$default = $this->db->get_row($sql);

		if (PerchUtil::count($default)) {
			$default['addressSlug'] = $type_slug;
			unset($default[$this->pk]);

			$newID = $this->db->insert($this->table, $default);

			$Adr = $this->find($newID);

			return $Adr;
		}

		return false;
	}

	public function deprecate_default_address($customerID)
	{
		$sql = 'SELECT * FROM '.$this->table.' WHERE customerID='.$this->db->pdb((int)$customerID).' AND addressSlug='.$this->db->pdb('default');
		$Address = $this->return_instance($this->db->get_row($sql));

		if ($Address) {
			$Address->reslug();
		}
	}

	public function freeze_for_order($addressID, $orderID)
	{
		$sql = 'SELECT * FROM '.$this->table.' WHERE addressID='.$this->db->pdb((int)$addressID);
		$row = $this->db->get_row($sql);

		if (PerchUtil::count($row)) {
			unset($row['addressID']);
			$row['orderID'] = (int)$orderID;

			return $this->db->insert($this->table, $row);
		}

		return null;
	}

	public function standard_where_callback(PerchQuery $Query, $opts = null)
    {
    	if ($this->deleted_date_column) {
    		 $Query->where[] = $this->deleted_date_column . ' IS NULL';
		}

		if (isset($opts['defeat-restrictions']) && $opts['defeat-restrictions']) {
			// don't restrict
		} else {
			$Query->where[] =  'orderID IS NULL';	
		}

		
        return $Query;
    }


}
