<?php

class PerchShop_OrderItems extends PerchShop_Factory
{
	public $api_method             = 'orders';
	public $api_list_method        = 'orders';
	public $singular_classname     = 'PerchShop_OrderItem';
	public $static_fields          = ['orderID', 'productID'];
	public $remote_fields          = ['id', 'sku', 'price', 'qty', 'tax_rate', 'tax_band'];
	
	protected $table               = 'shop_order_items';
	protected $pk                  = 'itemID';
	protected $index_table         = false;
	protected $master_template	   = 'shop/orders/item.html';
	
	protected $default_sort_column = 'itemID';

	protected $event_prefix = 'shop.orderitem';


	public function get_for_admin($orderID)
	{
		$sql = 'SELECT oi.*, p.* 
				FROM '.$this->table.' oi, '.PERCH_DB_PREFIX.'shop_products p
				WHERE 
					oi.itemType='.$this->db->pdb('product').'
					AND oi.productID = p.productID
					AND orderID='.$this->db->pdb((int)$orderID);

		return $this->return_instances($this->db->get_rows($sql));
	}

	public function copy_from_cart($orderID, $Cart, $cart_data)
	{
		if (isset($cart_data['items']) && PerchUtil::count($cart_data['items'])) {
			foreach($cart_data['items'] as $item) {

				$data = [
					'itemType'        => 'product',
					'orderID'         => $orderID,
					'productID'       => $item['id'],
					'itemPrice'       => $item['price_without_tax'],
					'itemTax'         => $item['tax'],
					'itemTotal'       => $item['price_with_tax'],
					'itemQty'         => $item['qty'],
					'itemTaxRate'     => $item['tax_rate'],
					'itemDiscount'    => $item['discount'],
					'itemTaxDiscount' => $item['tax_discount'],
				];
				$this->create($data);
			}
		}

		// Shipping
		if (isset($cart_data['shipping_id']) && $cart_data['shipping_id']!='') {
			$data = [
				'itemType'        => 'shipping',
				'orderID'         => $orderID,
				'shippingID'      => $cart_data['shipping_id'],
				'itemPrice'       => $cart_data['shipping_without_tax'],
				'itemTax'         => $cart_data['shipping_tax'],
				'itemTotal'       => $cart_data['shipping_with_tax'],
				'itemQty'         => 1,
				'itemTaxRate'     => $cart_data['shipping_tax_rate'],
				'itemDiscount'    => $cart_data['total_shipping_discount'],
				'itemTaxDiscount' => $cart_data['total_shipping_tax_discount'],
			];
			$this->create($data);
		}
	}

}
