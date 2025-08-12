<?php

class PerchShop_Email extends PerchShop_Base
{
	protected $factory_classname = 'PerchShop_Emails';
	protected $table             = 'shop_emails';
	protected $pk                = 'emailID';
	protected $index_table       = 'shop_index';

	protected $modified_date_column = 'emailUpdated';

	protected $duplicate_fields  = array('emailTitle'=>'name', 'emailSlug'=>'slug', 'emailActive'=>'enabled');

	protected $event_prefix = 'shop.email';

}