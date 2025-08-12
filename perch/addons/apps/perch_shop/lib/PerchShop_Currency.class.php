<?php

class PerchShop_Currency extends PerchShop_Base
{
	protected $factory_classname = 'PerchShop_Currencies';
	protected $table             = 'shop_currencies';
	protected $pk                = 'currencyID';

	protected $event_prefix = 'shop.currency';

	public function update($data)
	{
		if (!PERCH_RUNWAY) {
			if (isset($data['currencyActive']) && $data['currencyActive']) {
				$this->db->execute('UPDATE '.$this->table.' SET currencyActive=0');
			}	
		}
		
		return parent::update($data);
	}

	public function get_formatted($number=0)
	{
		return $this->format_display($number);
	}

	public function format_numeric($number=0)
	{
		if (!is_numeric($number)) {
			$number = 0;
		}
		
		return number_format($number, $this->currencyDecimals(), '.', '');
	}

	public function format_display($number=0)
	{
		if ($this->currencySymbolPosition() == 'before') {
			return $this->currencySymbol().''.number_format((float)$number, 
															$this->currencyDecimals(),
															$this->currencyDecimalSeparator(),
															$this->currencyThousandsSeparator()
															);
		}else{
			return number_format((float)$number, 
								$this->currencyDecimals(),
								$this->currencyDecimalSeparator(),
								$this->currencyThousandsSeparator()
								).$this->currencySymbol();
		}
		
	}

	public function is_default()
	{
		$Settings = $this->api->get('Settings');
		$default_currency = $Settings->get('perch_shop_default_currency')->val();
		return ($this->id() == $default_currency);
	}
}