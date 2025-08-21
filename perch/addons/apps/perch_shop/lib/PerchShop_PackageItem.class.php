<?php

class PerchShop_PackageItem extends PerchShop_Base
{
    protected $factory_classname = 'PerchShop_PackageItems';
    protected $table             = 'shop_package_items';
    protected $pk                = 'itemID';
    protected $index_table       = false;

    protected $event_prefix = 'shop.packageitem';
}

