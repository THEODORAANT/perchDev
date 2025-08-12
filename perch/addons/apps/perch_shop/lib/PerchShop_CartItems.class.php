<?php

class PerchShop_CartItems extends PerchShop_Factory
{
	public $singular_classname     = 'PerchShop_CartItem';
	protected $table               = 'shop_cart_items';
	protected $pk                  = 'itemID';

	protected $default_sort_column = 'itemID';

	protected $event_prefix = 'shop.cartitem';

	public function get_for_cart($cartID)
	{
		return $this->get_by('cartID', $cartID);
	}

}