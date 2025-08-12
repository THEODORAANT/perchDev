<?php

class PerchShop_ShippingZone extends PerchShop_Base
{
    protected $table        = 'shop_shipping_zones';
    protected $pk           = 'zoneID';

    protected $event_prefix = 'shop.shipping_zone';

    protected $duplicate_fields  = [
									'zoneTitle' => 'title', 
									'zoneSlug' => 'slug',
									'zoneActive' => 'status',
									'zoneIsDefault' => 'zoneIsDefault',
								   ];

								   

    public function to_array()
	{
		$details = parent::to_array();

		$TaxRates = new PerchShop_TaxRates($this->api);
		$details['rates'] = $TaxRates->get_edit_values($this->id());

		return $details;
	}

	public function update($data)
	{
		if (isset($data['zoneIsDefault']) && $data['zoneIsDefault']) {
			$this->db->execute('UPDATE '.$this->table.' SET zoneIsDefault=0');
		}

		$r = parent::update($data);

		$countries = $this->get('countries');
		$this->update_country_data($countries);

		return $r;
	}

	private function update_country_data($countries)
	{
		$sql = 'DELETE FROM '.PERCH_DB_PREFIX.'shop_shipping_zone_countries WHERE zoneID='.$this->db->pdb($this->id());
		$this->db->execute($sql);

		if (PerchUtil::count($countries)) {
			foreach($countries as $countryID){
				$this->db->insert(PERCH_DB_PREFIX.'shop_shipping_zone_countries', [
					'countryID' => (int)$countryID,
					'zoneID' => (int)$this->id(),
				]);
			}
		}
	}

}