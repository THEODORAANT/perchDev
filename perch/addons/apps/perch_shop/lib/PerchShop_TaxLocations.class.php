<?php

class PerchShop_TaxLocations extends PerchShop_Factory
{
    protected $table               = 'shop_tax_locations';
    protected $pk                  = 'locationID';
    protected $singular_classname  = 'PerchShop_TaxLocation';

    public $static_fields          = ['locationTitle', 'countryID', 'regionID', 'locationTaxRate', 'locationTaxRateReduced', 'locationIsHome', 'locationIsDefault'];

    protected $namespace           = 'shop';
    protected $event_prefix        = 'shop.tax';

    protected $default_sort_column = 'locationIsHome DESC, locationIsDefault ASC, locationTitle';
	protected $created_date_column = 'locationCreated';

	public static function get_list_options()
	{
		$API  = new PerchAPI(1.0, 'perch_shop');
		$DB = $API->get('DB');

		$sql = 'SELECT locationID, locationTitle FROM '.PERCH_DB_PREFIX.'shop_tax_locations ORDER BY locationIsHome DESC, locationIsDefault ASC, locationTitle ASC';
		$rows = $DB->get_rows($sql);

		if (PerchUtil::count($rows)) {
			foreach($rows as $row) {
				$out[] = $row['locationTitle'].'|'.$row['locationID'];
			}
			return implode(',', $out);
		}

		return false;
	}

	public function find_matching($countryID=null, $regionID=null)
	{
		$sql = 'SELECT * FROM '.$this->table.'
				WHERE locationDeleted IS NULL AND ((countryID='.$this->db->pdb((int)$countryID);

		if ($regionID) {
			$sql .= ' AND regionID='.$this->db->pdb((int)$regionID);
		}else{
			$sql .= ' AND regionID IS NULL';
		}

		$sql .= ') OR locationIsDefault=1) ORDER BY locationIsDefault ASC LIMIT 1';

		return $this->return_instance($this->db->get_row($sql));
	}
}