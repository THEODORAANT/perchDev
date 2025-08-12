<?php

class PerchShop_OptionValue extends PerchShop_Base
{
	protected $factory_classname = 'PerchShop_OptionValues';
	protected $table             = 'shop_option_values';
	protected $pk                = 'valueID';
	protected $index_table       = 'shop_index';

	protected $modified_date_column = 'valueUpdated';
	public $deleted_date_column  = 'valueDeleted';

	protected $event_prefix = 'shop.option.value';

	protected $duplicate_fields  = array(
									'valueTitle'=>'title',
									'valueModPrice'=>'mod_price',
									);


}