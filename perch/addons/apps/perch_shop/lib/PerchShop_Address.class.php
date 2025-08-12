<?php

class PerchShop_Address extends PerchShop_Base
{
	protected $factory_classname = 'PerchShop_Addresses';
	protected $table             = 'shop_addresses';
	protected $pk                = 'addressID';
	#protected $index_table       = 'shop_admin_index';

	protected $modified_date_column = 'addressUpdated';
	public $deleted_date_column  = 'addressDeleted';

	protected $duplicate_fields  = array('addressTitle'=>'title', 'addressSlug'=>'*title', 'addressFirstName'=>'first_name', 'addressLastName'=>'last_name', 'addressCompany'=>'company', 'addressLine1'=>'address_1', 'customerID'=>'customer', 'countryID'=>'country');

	protected $event_prefix = 'shop.address';


	public function to_array()
	{
		$out = parent::to_array();

		$out['country_name'] = $this->get_country_name();

		return $out;
	}

	public function linearise()
	{
		$out = '';

		$parts = [];
		$parts[] = $this->address_1();
		$parts[] = $this->address_2();
		$parts[] = $this->city();
		$parts[] = $this->county();
		$parts[] = $this->postcode();

		$Country = $this->get_country();
		if ($Country) {
			$parts[] = $Country->country();
		}

		$out = implode(', ', $parts);

		while(strpos($out, ', , ')!==false) {
			$out = str_replace(', , ', ', ', $out);
		}
		
		return $out;
	}

	public function format_for_template($prefix='billing')
	{
		$data = $this->to_array();
		$out  = [];

		if (PerchUtil::count($data)){
			foreach($data as $key=>$value) {
				if (substr($key, 0, 5)=='perch') {
					continue;
				}
				if (is_null($prefix)) {
					$out[$key] = $value;
				}else{
					$out[$prefix.'_'.$key] = $value;	
				}
			}
		}

		return $out;
	}


	public function get_country()
	{
		$Countries = new PerchShop_Countries($this->api);
		return $Countries->find($this->countryID());
	}

	public function get_country_name()
	{
		$Country = $this->get_country();
		if ($Country) {
			return $Country->country();
		}
		PerchUtil::debug('No country set for address (name)', 'error');
		return false;
	}

	public function get_country_iso2()
	{
		$Country = $this->get_country();
		if ($Country) {
			return $Country->iso2();	
		}
		PerchUtil::debug('No country set for address (iso2)', 'error');
		return false;
		
	}

	public function reslug()
	{
		$slug = PerchUtil::urlify($this->id().' '.strftime('%d %b %Y', strtotime($this->addressCreated())));

		if ($this->addressTitle() == $this->addressSlug()) {
			$title = $slug;
		}else{
			$title = $this->addressTitle();
		}

		$this->update([
			'addressSlug' => $slug,
			'addressTitle' => $title,
			]);
	}
}