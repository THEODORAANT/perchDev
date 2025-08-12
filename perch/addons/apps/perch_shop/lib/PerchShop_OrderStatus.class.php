<?php

class PerchShop_OrderStatus extends PerchShop_Base
{
	protected $factory_classname = 'PerchShop_OrderStatuses';
	protected $table             = 'shop_order_statuses';
	protected $pk                = 'statusID';

	protected $modified_date_column = 'statusUpdated';
	public $deleted_date_column  = 'statusDeleted';

    protected $date_fields = ['statusUpdated', 'statusCreated'];

	protected $duplicate_fields  = [];

	protected $event_prefix = 'shop.orderstatus';

}