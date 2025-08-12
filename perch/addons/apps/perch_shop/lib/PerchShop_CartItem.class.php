<?php

class PerchShop_CartItem extends PerchShop_Base
{
	protected $factory_classname = 'PerchShop_CartItems';
	protected $table             = 'shop_cart_items';
	protected $pk                = 'itemID';
	protected $index_table       = '';

	protected $event_prefix = 'shop.cartitem';


}