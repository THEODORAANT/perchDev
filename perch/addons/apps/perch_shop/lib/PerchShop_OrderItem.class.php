<?php

class PerchShop_OrderItem extends PerchShop_Base
{
	protected $factory_classname = 'PerchShop_OrderItems';
	protected $table             = 'shop_order_items';
	protected $pk                = 'itemID';
	protected $index_table       = false;

	protected $duplicate_fields  = [
										'productID'   => 'id',
										'itemPrice'   => 'price',
										'itemQty' 	  => 'qty',
										'itemTaxRate' => 'tax_rate',
									];

	protected $event_prefix = 'shop.orderitem';

    public function title()
    {
        return $this->details['title'];
    }

    public function is_variant()
    {
        return $this->details['parentID']>0;
    }
}