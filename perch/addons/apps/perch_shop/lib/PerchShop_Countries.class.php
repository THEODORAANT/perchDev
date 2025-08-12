<?php

class PerchShop_Countries extends PerchAPI_Factory
{
	protected $singular_classname  = 'PerchShop_Country';
	protected $table               = 'shop_countries';
	protected $pk                  = 'countryID';
	protected $namespace           = 'shop';
	
	protected $default_sort_column = 'country';  
	
	public $static_fields          = array('countryID', 'country', 'iso2', 'iso3', 'isonum', 'eu', 'countryActive');
	
	protected $event_prefix        = 'shop.country';


	public static function get_list_options()
	{
		$API  = new PerchAPI(1.0, 'perch_shop');
		$DB = $API->get('DB');

		$sql = 'SELECT country, countryID FROM '.PERCH_DB_PREFIX.'shop_countries WHERE countryActive=1 ORDER BY country ASC';
		$rows = $DB->get_rows($sql);

		if (PerchUtil::count($rows)) {
			foreach($rows as $row) {
				$out[] = $row['country'].'|'.$row['countryID'];
			}
			return implode(',', $out);
		}

		return false;
	}

	public function country_name($countryID)
	{
		$Country = $this->find($countryID);
		if ($Country) {
			return $Country->country();
		}
		return '';
	}
}