<?php

class PerchShop_Sale extends PerchShop_Base
{
	protected $factory_classname = 'PerchShop_Sales';
	protected $table             = 'shop_sales';
	protected $pk                = 'saleID';

	protected $modified_date_column = 'saleUpdated';

	protected $event_prefix = 'shop.sale';

	protected $duplicate_fields  = [
									'saleTitle'  => 'title', 
									'saleActive' => 'active',
									'saleFrom'   => 'from',
									'saleTo'     => 'to',
								   ];

}