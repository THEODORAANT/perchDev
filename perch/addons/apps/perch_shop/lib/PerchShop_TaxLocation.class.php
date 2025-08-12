<?php

class PerchShop_TaxLocation extends PerchShop_Base
{
    protected $table        = 'shop_tax_locations';
    protected $pk           = 'locationID';

    protected $event_prefix = 'shop.tax';

    public $deleted_date_column  = 'locationDeleted';

    public function to_array()
	{
		$details = parent::to_array();

		$TaxRates = new PerchShop_TaxRates($this->api);
		$details['rates'] = $TaxRates->get_edit_values($this->id());

		return $details;
	}

	public function update($data)
	{
		if (isset($data['locationIsDefault']) && $data['locationIsDefault']) {
			$this->db->execute('UPDATE '.$this->table.' SET locationIsDefault=0');
		}
		return parent::update($data);
	}

	public function is_in_eu()
	{
		$Countries = new PerchShop_Countries($this->api);
		$Country = $Countries->find((int)$this->countryID());

		return PerchUtil::bool_val($Country->eu());
	}
}