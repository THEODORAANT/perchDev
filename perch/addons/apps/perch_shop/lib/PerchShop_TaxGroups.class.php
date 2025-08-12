<?php

class PerchShop_TaxGroups extends PerchShop_Factory
{
    protected $table               = 'shop_tax_groups';
    protected $pk                  = 'groupID';
    protected $singular_classname  = 'PerchShop_TaxGroup';

    protected $namespace           = 'shop';
    protected $event_prefix        = 'shop.tax';

    protected $default_sort_column = 'groupTitle';

    public $static_fields          = ['groupTitle', 'groupSlug', 'groupTaxRate'];

	protected $created_date_column = 'groupCreated';
}