<?php

class PerchShop_Brands extends PerchShop_Factory
{
	public $api_method             = 'brands';
	public $api_list_method        = 'brands';
	public $singular_classname     = 'PerchShop_Brand';
	public $static_fields          = ['brandTitle', 'brandCreated'];
	public $remote_fields          = ['title', 'slug', 'status', 'description'];
	
	protected $table               = 'shop_brands';
	protected $pk                  = 'brandID';
	protected $index_table         = 'shop_index';
	protected $master_template	   = 'shop/brands/brand.html';
	
	protected $default_sort_column = 'brandTitle';
	protected $created_date_column = 'brandCreated';
	public $deleted_date_column    = 'brandDeleted';

	protected $event_prefix = 'shop.brand';

	protected $runtime_restrictions = [
		[
			'field'          => 'status',
			'values'         => ['1'],
			'negative_match' => false,
			'match'          => 'all',
			'fuzzy'			 => false
		]
	];
}