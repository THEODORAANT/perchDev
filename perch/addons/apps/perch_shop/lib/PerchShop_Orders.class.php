<?php

class PerchShop_Orders extends PerchShop_Factory
{
	public $api_method             = 'orders';
	public $api_list_method        = 'orders';
	public $singular_classname     = 'PerchShop_Order';
	public $static_fields          = ['orderStatus', 'orderTotal', 'orderGateway', 'orderCurrency', 'orderExchangeRate', 'customerID', 'orderGatewayRef'];
	public $remote_fields          = ['customer', 'gateway', 'status', 'subtotal', 'shipping_price', 'total', 'currency', 'currency_code', 'exchange_rate', 'ship_to', 'bill_to', 'shipping'];

	protected $table               = 'shop_orders';
	protected $pk                  = 'orderID';
	#protected $index_table         = 'shop_admin_index';
	protected $master_template	   = 'shop/orders/order.html';

	protected $default_sort_column = 'orderCreated';
	protected $created_date_column = 'orderCreated';

	protected $event_prefix = 'shop.order';

	private $customerID = false;


	public function find_runtime_for_customer($orderID, $Customer)
	{
		$Statuses = new PerchShop_OrderStatuses($this->api);
		$sql = 'SELECT * FROM '.$this->table.' 
				WHERE customerID='.$this->db->pdb((int)$Customer->id()).' 
					AND orderID='.$this->db->pdb((int)$orderID).' 
					AND orderStatus IN ('.$this->db->implode_for_sql_in($Statuses->get_status_and_above('paid')).')';
		return $this->return_instance($this->db->get_row($sql));
	}

	public function create_from_cart($Cart, $gateway, $Customer, $BillingAddress, $ShippingAddress)
	{
		

		$cart_data = $Cart->calculate_cart();

		// check shipping
		if (!isset($cart_data['shipping_id'])) {
			$cart_data['shipping_id'] = null;
		} 

		$data = [
			'orderStatus'               => 'created',
			'orderGateway'              => $gateway,
			'orderTotal'                => $cart_data['grand_total'],
			'currencyID'                => $cart_data['currency_id'],
			'orderItemsSubtotal'        => $cart_data['total_items'],
			'orderItemsTax'             => $cart_data['total_items_tax'],
			'orderItemsTotal'           => $cart_data['total_items'] + $cart_data['total_items_tax'],
			'orderShippingSubtotal'     => $cart_data['shipping_without_tax'],
			'orderShippingDiscounts'    => $cart_data['total_shipping_discount'],
			'orderShippingTax'          => $cart_data['shipping_tax'],
			'orderShippingTaxDiscounts' => $cart_data['total_shipping_tax_discount'],
			'orderShippingTotal'        => $cart_data['shipping_with_tax'],
			'orderDiscountsTotal'       => $cart_data['total_discounts'],
			'orderTaxDiscountsTotal'    => $cart_data['total_tax_discount'],
			'orderSubtotal'             => $cart_data['total_items_with_shipping'] - $cart_data['total_discounts'],
			'orderTaxTotal'             => $cart_data['total_tax'],
			'orderItemsRefunded'        => 0,
			'orderTaxRefunded'          => 0,
			'orderShippingRefunded'     => 0,
			'orderTotalRefunded'        => 0,
			'orderTaxID'                => ($Customer->customerTaxIDStatus()=='valid' ? $Customer->customerTaxID() : null),
			'orderShippingWeight'       => $cart_data['shipping_weight'],
			'orderCreated'              => gmdate('Y-m-d H:i:s'),
			'orderPricing'              => $Cart->get_cart_field('cartPricing'),
			'orderDynamicFields'        => $Cart->get_cart_field('cartProperties'),
			'customerID'                => $Customer->id(),
			'shippingID'                => $cart_data['shipping_id'],
			'orderShippingTaxRate'      => $cart_data['shipping_tax_rate'],
			'orderBillingAddress'       => $BillingAddress->id(),
			'orderShippingAddress'      => $ShippingAddress->id(),
		];

		$Order = $this->create($data);

		if (is_object($Order)) {
			$Order->freeze_addresses();
			$Order->copy_order_items_from_cart($Cart, $cart_data);
			$Cart->add_order_id_to_stashed_data($Order->id());
		}

		$Perch = Perch::fetch();
		$Perch->event('shop.order_create', $Order);
        $Perch->event('shop.order_status_update', $Order, 'created');

		return $Order;
		
	}

	public function find_with_gateway_ref($str)
	{
		$sql = 'SELECT * FROM '.$this->table.'
				WHERE orderGatewayRef LIKE '.$this->db->pdb('%'.$str.'%').'
				LIMIT 1';
		$row = $this->db->get_row($sql);
		return $this->return_instance($row);
	}

	public function get_with_products($product_ids)
	{
		$product_ids = array_map("intval", $product_ids);
		$sql = 'SELECT o.*, cd.cartData, c.*
				FROM '.$this->table.' o, '.PERCH_DB_PREFIX.'shop_cart_data cd, '.PERCH_DB_PREFIX.'shop_customers c
				WHERE o.orderID=cd.orderID AND o.customerID=c.customerID
					AND o.orderStatus='.$this->db->pdb('paid').'
					AND cd.productID IN ('.$this->db->implode_for_sql_in($product_ids).')';
		$rows = $this->db->get_rows($sql);

		return $this->return_instances($rows);
	}

	public function get_admin_listing($status=array('paid'), $Paging=false)
	{
		$sort_val = null;
        $sort_dir = null;


		if ($Paging && $Paging->enabled()) {
            $sql = $Paging->select_sql();
            list($sort_val, $sort_dir) = $Paging->get_custom_sort_options();
        }else{
            $sql = 'SELECT';
        }

        $sql .= ' o.*, c.*, CONCAT(customerFirstName, " ", customerLastName) AS customerName
                FROM ' . $this->table .' o, '.PERCH_DB_PREFIX.'shop_customers c
                WHERE o.customerID=c.customerID
                	AND o.orderDeleted IS NULL 
                	AND o.orderStatus IN ('.$this->db->implode_for_sql_in($status).')';

		if ($sort_val) {
            $sql .= ' ORDER BY '.$sort_val.' '.$sort_dir;
        } else {
	        if (isset($this->default_sort_column)) {
	            $sql .= ' ORDER BY o.orderCreated DESC ';
	        }
	    }

        if ($Paging && $Paging->enabled()) {
            $sql .=  ' '.$Paging->limit_sql();
        }

        $results = $this->db->get_rows($sql);

        if ($Paging && $Paging->enabled()) {
            $Paging->set_total($this->db->get_count($Paging->total_count_sql()));
        }

        return $this->return_instances($results);
	}

	public function get_dashboard_widget()
	{
		$Statuses = new PerchShop_OrderStatuses($this->api);
		$sql = 'SELECT o.orderID, o.orderCreated, o.orderInvoiceNumber, o.orderTotal, o.currencyID, o.orderExchangeRate, c.customerID, c.customerFirstName, c.customerLastName, cur.*
				FROM '.PERCH_DB_PREFIX.'shop_orders o, '.PERCH_DB_PREFIX.'shop_customers c, '.PERCH_DB_PREFIX.'shop_currencies cur
				WHERE o.customerID=c.customerID AND o.currencyID=cur.currencyID
					AND o.orderStatus IN ('.$this->db->implode_for_sql_in($Statuses->get_status_and_above('paid')).')
					AND o.orderDeleted IS NULL 
				ORDER BY o.orderCreated DESC
				LIMIT 10';
		$rows = $this->db->get_rows($sql);

		$out = [];
		$out['items'] = $rows;


		return $out;
	}

	public function get_revenue_dashboard_widget()
	{
		$Statuses = new PerchShop_OrderStatuses($this->api);
		$sql = 'SELECT DATE_FORMAT(o.orderCreated, "%Y-%m-01") AS orderMonth, SUM(o.orderTotal / o.orderExchangeRate) AS revenue
				FROM '.PERCH_DB_PREFIX.'shop_orders o
				WHERE o.orderStatus IN ('.$this->db->implode_for_sql_in($Statuses->get_status_and_above('paid')).')
					AND o.orderDeleted IS NULL 
				GROUP BY orderMonth
				ORDER BY o.orderCreated DESC
				LIMIT 10';
		$rows = $this->db->get_rows($sql);

		$out = [];
		$out['items'] = $rows;


		return $out;

	}
}