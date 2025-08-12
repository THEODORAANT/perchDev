<?php

class PerchShop_TaxRate extends PerchShop_Base
{
    protected $table        = 'shop_tax_rates';
    protected $pk           = 'rateID';

    protected $event_prefix = 'shop.tax';

    protected $modified_date_column = 'rateUpdated';
	public $deleted_date_column  = 'rateDeleted';
}