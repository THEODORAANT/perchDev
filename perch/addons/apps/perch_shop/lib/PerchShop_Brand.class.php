<?php

class PerchShop_Brand extends PerchShop_Base
{
	protected $factory_classname = 'PerchShop_Brands';
	protected $table             = 'shop_brands';
	protected $pk                = 'brandID';
	protected $index_table       = 'shop_index';

	protected $modified_date_column = 'brandUpdated';
	public $deleted_date_column  = 'brandDeleted';

	protected $duplicate_fields  = array('brandTitle'=>'title');

	protected $event_prefix = 'shop.brand';

}