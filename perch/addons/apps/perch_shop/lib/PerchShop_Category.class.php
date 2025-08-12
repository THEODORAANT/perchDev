<?php

class PerchShop_Category extends PerchShop_Base
{
	protected $factory_classname = 'PerchShop_Categories';
	protected $table             = 'shop_categories';
	protected $pk                = 'categoryID';
	protected $index_table       = 'shop_index';

	protected $modified_date_column = 'categoryUpdated';

}