<?php

class PerchShop_Currencies extends PerchShop_Factory
{
	public $singular_classname     = 'PerchShop_Currency';
	public $static_fields          = ['currencyID', 'currencyCode', 'currencyTitle', 'currencyNumber', 'currencySymbol', 'currencyDecimals', 'currencyRate', 'currencyActive', 'currencySymbolPosition', 'currencyDecimalSeparator', 'currencyThousandsSeparator'];

	protected $table               = 'shop_currencies';
	protected $pk                  = 'currencyID';
	protected $master_template	   = 'shop/currencies/currency.html';

	protected $default_sort_column = 'currencyActive DESC, currencyIsCommon DESC, currencyCode';

	protected $runtime_restrictions = [
		[
			'field'          => 'currencyActive',
			'values'         => ['1'],
			'negative_match' => false,
			'match'          => 'all',
			'fuzzy'			 => false
		]
	];

	protected $event_prefix = 'shop.currency';

	public static function get_settings_select_list($Form, $id, $details, $setting)
	{
		$opts = array();
		$opts[] = array('value'=>'', 'label'=>'');
		$c = __CLASS__;
		$Currencies = new $c;
		$currencies = $Currencies->get_by('currencyActive', '1');
		if (PerchUtil::count($currencies)) {
			foreach($currencies as $Currency) {
				$opts[] = array('value'=>$Currency->currencyID(), 'label'=>$Currency->currencyCode());
			}
		}
        return $Form->select($id, $opts, $Form->get($details, $id, $setting['default'])); 
	}

	public function get_reporting_currency()
	{
		$Settings = $this->api->get('Settings');
		$reporting_currency = $Settings->get('perch_shop_reporting_currency')->val();

		$sql = 'SELECT * FROM '.$this->table.'
				WHERE currencyActive=1
					AND currencyID='.(int)$reporting_currency;
		return $this->return_instance($this->db->get_row($sql));
	}

	public function get_active()
	{
		$Settings = $this->api->get('Settings');
		$default_currency = $Settings->get('perch_shop_default_currency')->val();

		$sql = 'SELECT * FROM '.$this->table.'
				WHERE currencyActive=1
				ORDER BY currencyID='.(int)$default_currency.' DESC, currencyCode';
		return $this->return_instances($this->db->get_rows($sql));
	}

	public function find_by_code($code)
	{
		$code = strtoupper($code);
		
		$sql = 'SELECT * FROM '.$this->table.'
				WHERE currencyActive=1
				AND currencyCode='.$this->db->pdb($code).'
				LIMIT 1';
		return $this->return_instance($this->db->get_row($sql));
	}

	public function get_default()
	{
		$Settings = $this->api->get('Settings');
		$default_currency = $Settings->get('perch_shop_default_currency')->val();

		$sql = 'SELECT * FROM '.$this->table.'
				WHERE currencyActive=1
					AND currencyID='.(int)$default_currency;
		return $this->return_instance($this->db->get_row($sql));

	}

	public static function get_list_options()
	{
		$API  = new PerchAPI(1.0, 'perch_shop');
		$DB = $API->get('DB');

		$Settings = $API->get('Settings');
		$default_currency = $Settings->get('perch_shop_default_currency')->val();

		$sql = 'SELECT currencyID, currencyCode FROM '.PERCH_DB_PREFIX.'shop_currencies 
				WHERE currencyActive=1
				ORDER BY currencyID='.(int)$default_currency.' DESC, currencyCode';
		$rows = $DB->get_rows($sql);

		if (PerchUtil::count($rows)) {
			foreach($rows as $row) {
				$out[] = $row['currencyCode'].'|'.$row['currencyID'];
			}
			return implode(',', $out);
		}

		return false;
	}
}


