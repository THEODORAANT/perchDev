<?php

class PerchShop_TaxGroup extends PerchShop_Base
{
    protected $table        = 'shop_tax_groups';
    protected $pk           = 'groupID';

    protected $event_prefix = 'shop.tax';

    public $deleted_date_column  = 'groupDeleted';

}