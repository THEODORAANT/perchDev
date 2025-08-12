<?php

class PerchShop_Option extends PerchShop_Base
{
	protected $factory_classname = 'PerchShop_Options';
	protected $table             = 'shop_options';
	protected $pk                = 'optionID';
	protected $index_table       = 'shop_index';

	protected $event_prefix = 'shop.product.option';

	protected $modified_date_column = 'optionUpdated';
	public $deleted_date_column  = 'optionDeleted';

	protected $duplicate_fields  = array(
									'optionTitle' => 'title',
									'optionType'  => 'type',
									'optionPrecendence' => 'precendence',
									);


	public function to_array()
	{
		$details = parent::to_array();

		$Values = new PerchShop_OptionValues($this->api);
		$details['options'] = $Values->get_edit_values($this->id());

		return $details;
	}

	public function get_value_ids()
	{
		$Values = new PerchShop_OptionValues($this->api);
		return $Values->get_edit_ids($this->id());
	}

	public function get_values_for_product($productID)
	{
		$Values = new PerchShop_OptionValues($this->api);
		return $Values->get_for_product($this->id(), $productID);
	}

}