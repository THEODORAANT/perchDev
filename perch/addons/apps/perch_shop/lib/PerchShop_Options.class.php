<?php

class PerchShop_Options extends PerchShop_Factory
{
	public $singular_classname     = 'PerchShop_Option';
	public $static_fields          = ['optionTitle', 'optionPrecendence', 'optionCreated'];

	protected $table               = 'shop_options';
	protected $pk                  = 'optionID';
	protected $index_table         = 'shop_index';
	protected $master_template	   = 'shop/products/option.html';

	protected $default_sort_column = 'optionPrecendence';
	protected $created_date_column = 'optionCreated';

	protected $event_prefix = 'shop.product.option';

	public $productID = false;

	public function get_checkbox_values($productID)
	{
		$sql = 'SELECT optionID FROM '.PERCH_DB_PREFIX.'shop_product_options WHERE productID='.$this->db->pdb((int)$productID);
		return $this->db->get_rows_flat($sql);
	}

	public function get_for_product($productID)
	{
		$sql = 'SELECT o.* FROM '.$this->table.' o, '.PERCH_DB_PREFIX.'shop_product_options po 
				WHERE o.optionID=po.optionID AND po.productID='.$this->db->pdb((int)$productID).'
					AND o.optionDeleted IS NULL
				ORDER BY optionPrecendence ASC';
		return $this->return_instances($this->db->get_rows($sql));
	}

	public function get_checkbox_options()
	{
		$sql = 'SELECT optionID, optionTitle FROM '.$this->table.' WHERE optionDeleted IS NULL ORDER BY optionTitle ASC';
		$rows = $this->db->get_rows($sql);

		if (PerchUtil::count($rows)) {
			$opts = [];
			foreach($rows as $option) {
				$opts[] = [
					'value' => $option['optionID'],
					'label' => $option['optionTitle'],
				];
			}
			return $opts;
		}

		return false;
	}

	public function get_for_product_template($productID)
	{
		$options = $this->get_for_product($productID);

		$out = [];

		if (PerchUtil::count($options)) {

			$Values = new PerchShop_OptionValues($this->api);

			foreach($options as $Option) {
				$tmp = $Option->to_array();

				$values = $Values->get_for_product($Option->id(), $productID);

				$vals = [];
				if (PerchUtil::count($values)) {
					foreach($values as $Value) {
						$vals[] = $Value->to_array();
					}
				}

				$tmp['productvalues'] = $vals;

				$out[] = $tmp;
			}
		}

		return $out;
	}

}