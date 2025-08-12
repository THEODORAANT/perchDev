<?php

class PerchShop_Customer extends PerchShop_Base
{
	protected $factory_classname = 'PerchShop_Customers';
	protected $table             = 'shop_customers';
	protected $pk                = 'customerID';
	protected $index_table       = 'shop_index';

	protected $modified_date_column = 'customerUpdated';
	public $deleted_date_column  = 'customerDeleted';

	protected $duplicate_fields  = array('customerFirstName'=>'first_name', 'customerLastName'=>'last_name', 'customerEmail'=>'email', 'customerTaxID'=>'taxID', 'customerTaxIDType'=>'taxID_type');

	protected $event_prefix = 'shop.customer';

	public function update($data)
	{
		// If tax ID is being updated (or looks like it might be) force it to be rechecked.
		if (isset($data['taxID']) || isset($data['customerTaxID'])) {
			$data['customerTaxIDStatus'] = 'unchecked';
			$data['customerTaxIDLastChecked'] = null;
		}

		return parent::update($data);
	}

	public function update_from_form($SubmittedForm)
    {
        $data = [];
        $data['customerDynamicFields'] = PerchUtil::json_safe_encode($SubmittedForm->data);
        $this->update($data);

        // Addresses?

        $data = $SubmittedForm->data;

		$BillingAddress = null;
		$ShippingAddress = null;

		$Addresses = new PerchShop_Addresses($this->api);

		// Billing Address
		$adr_data = [];

		foreach($Addresses->remote_fields as $field) {
			if (isset($data[$field])) {
				$adr_data[$field] = $data[$field];
			}
		}

		if (PerchUtil::count($adr_data) && isset($adr_data['address_1'])) {
			$BillingAddress = $Addresses->find_for_customer($this->id(), 'default');

			if ($BillingAddress) {
				$BillingAddress->update([
					'addressDynamicFields' => PerchUtil::json_safe_encode($adr_data),
				]);	
			}
		}

		// Shipping Address
		$adr_data = [];

		foreach($Addresses->remote_fields as $field) {
			if (isset($data['shipping_'.$field])) {
				$adr_data[$field] = $data['shipping_'.$field];
			}
		}

		if (PerchUtil::count($adr_data) && isset($adr_data['address_1'])) {

			$ShippingAddress = $Addresses->find_for_customer($this->id(), 'shipping');

			if ($ShippingAddress) {
				$ShippingAddress->update([
					'addressDynamicFields' => PerchUtil::json_safe_encode($adr_data),
				]);
			}
		}

    }

	public function validate_taxID()
	{
		if (defined('PERCH_SHOP_ASSISTANT') && PERCH_SHOP_ASSISTANT) {
			if ($this->customerTaxID() && $this->tax_id_needs_checking()) {

				$result = [
					'result' => 'NOT CHECKED',
					'status' => '0',
					'message' => 'Not checked',
				];

				switch(strtolower($this->customerTaxIDType())) {
					case 'vat':
						$result = PerchShopAssistant::vat_check($this->customerTaxID());
						break;
				}

				if ($result['status'] == '200') {
					$data = [
								'customerTaxIDStatus' => 'valid',
								'customerTaxIDLastChecked' => gmdate('Y-m-d H:i:s'),
								'customerTaxIDLastResponse' => $result['result'],
							];
				}else{
					$data = [
								'customerTaxIDStatus' => 'invalid',
								'customerTaxIDLastChecked' => gmdate('Y-m-d H:i:s'),
								'customerTaxIDLastResponse' => $result['message'],
							];
				}

				$this->update($data);

			}
		}
	}

	public function pays_tax(PerchShop_TaxLocation $CustomerTaxLocation, PerchShop_TaxLocation $HomeTaxLocation)
	{
		$this->validate_taxID();

		if ($this->customerTaxID() && $this->customerTaxIDStatus()=='valid') {

			switch(strtolower($this->customerTaxIDType())) {

				case 'vat':
					// both EU?
					if ($HomeTaxLocation->is_in_eu() && $CustomerTaxLocation->is_in_eu()) {
						// different country? 
						if ($CustomerTaxLocation->countryID() != $HomeTaxLocation->countryID()) {
							return false;
						}	
					}
					break;
			}
		}

		return true;
	}


	private function tax_id_needs_checking()
	{
		if ($this->customerTaxIDStatus()=='unchecked') return true;

		$last_checked = strtotime($this->customerTaxIDLastChecked());
		$now = time();
		$threshold = 60*60*24; // 24 hours

		return (($now - $last_checked) > $threshold);
	}
}