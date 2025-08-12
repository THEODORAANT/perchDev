<?php

use League\Csv\Reader;
use League\Csv\Writer;

class PerchShop_OrdersExport
{
	public $static_fields = ['export', 'status', 'start', 'end', 'save', 'format'];
	public $dynamic_fields_column = 'exportDynamicFields';

	private $api;

	private $options;

	public function __construct($api)
	{
		$this->api = $api;
		$this->db  = PerchDB::fetch();
	}

	public function populate($opts)
	{
		$this->options = $opts;

		if (!isset($this->options['format'])) {
			$this->options['format'] = 'default';
		}

		// if shop assistant isn't available, format is always 'default'
		if (!defined('PERCH_SHOP_ASSISTANT') || !PERCH_SHOP_ASSISTANT) {
			$this->options['format'] = 'default';
		}
	}

	public function export()
	{
		$data = [];

		switch($this->options['format']) {

			case 'xero':
				$this->options['export'] = 'orders'; // this is all we export for xero 
				$data = PerchShopAssistant_XeroExport::get_data($this->options);
				break;

			default:
				$sql = $this->get_query();
				$data = $this->db->get_rows($sql);
				break;

		}

		

		if (PerchUtil::count($data)) {

			$csv = Writer::createFromFileObject(new SplTempFileObject());

			switch($this->options['export']) {
				case 'addresses':
					$fields = array_keys($data[0]);
					unset($fields[array_search('json',$fields)]);

					if (PerchUtil::count($data)) {
						
						$Countries = new PerchShop_Countries($this->api);

						$out = [];

						foreach($data as $datum) {

							if (isset($datum['json'])) {

								$json = PerchUtil::json_safe_decode($datum['json'], true);
								unset($datum['json']);

								$row = [];

								if (PerchUtil::count($datum)) {
									foreach ($datum as $key=>$val) {
										$index = array_search($key, $fields);
										if ($index===false) {
											$fields[] = $key;
											$fields   = array_values($fields);
											$index    = array_search($key, $fields);
										}
										$row[$index] = $val;
									}
								}


								if (PerchUtil::count($json)) {
									foreach ($json as $key=>$val) {
										$index = array_search($key, $fields);
										if ($index===false) {
											$fields[] = $key;
											$fields   = array_values($fields);
											$index    = array_search($key, $fields);
										}

										switch($key) {
											case 'country' :
												$row[$index] = $Countries->country_name((int)$val);
												break;

											default:
												$row[$index] = $val;
												break;
										}
										
									}
								}

								// fill in the blanks
								for($i=0; $i<count($fields); $i++) {
									if (!isset($row[$i])) {
										$row[$i] = '';
									}
								}

								ksort($row);

								$out[] = $row;
							}
						}

						$data = $out;

						PerchUtil::debug($data);
						#return;

					}

					$csv->insertOne($fields);
					
					break;

				default:
					$csv->insertOne(array_keys($data[0]));
					break;
			}



			
			$csv->insertAll($data);


			$filename = $this->options['export'] .'_'.$this->options['status'].'_'.$this->options['start'].'_to_'.$this->options['end'].'.csv';

			switch ($this->options['save']) {
				case 'download':
					$csv->output($filename);
					die();
					break;

				default:

					break;
			}
		}
		

		
	}




	private function get_query()
	{
		$orders_table      = PERCH_DB_PREFIX.'shop_orders o';
		$items_table       = PERCH_DB_PREFIX.'shop_order_items oi';
		$adr_table         = PERCH_DB_PREFIX.'shop_addresses a';
		$customer_table    = PERCH_DB_PREFIX.'shop_customers c';
		$members_table     = PERCH_DB_PREFIX.'members m';
		$currency_table    = PERCH_DB_PREFIX.'shop_currencies cur';
		$country_table     = PERCH_DB_PREFIX.'shop_countries ctr';
		$products_table    = PERCH_DB_PREFIX.'shop_products p';
		$order_promo_table = PERCH_DB_PREFIX.'shop_order_promotions op';
		$promos_table 	   = PERCH_DB_PREFIX.'shop_promotions pr';
		
		$join_currencies   = "JOIN $currency_table ON o.currencyID=cur.currencyID";
		$join_customers    = "JOIN $customer_table ON o.customerID=c.customerID";
		$join_products     = "JOIN $products_table ON oi.productID=p.productID";
		$join_orders       = "JOIN $orders_table ON oi.orderID=o.orderID";
		$join_shipping_adr = "JOIN $adr_table ON o.orderShippingAddress=a.addressID";
		$join_billing_adr  = "JOIN $adr_table ON o.orderBillingAddress=a.addressID";
		$join_countries	   = "JOIN $country_table ON a.countryID=ctr.countryID";
		$join_promos       = "LEFT JOIN $order_promo_table ON op.orderID=o.orderID 
								LEFT JOIN $promos_table ON pr.promoID=op.promoID";

		$date_range = '(o.orderCreated BETWEEN '.$this->db->pdb($this->options['start'].' 00:00:00').' AND '.$this->db->pdb($this->options['end'].' 23:59:59').')';
		$status = 'o.orderStatus='.$this->db->pdb($this->options['status']);

		switch ($this->options['export']) {

			case 'orders':
				$sql = "SELECT 
						o.orderID AS 'Order ID',
						o.orderCreated AS 'Date UTC',
						o.orderStatus AS 'Status',
						o.orderGateway AS 'Gateway',
						cur.currencyCode AS 'Currency',
						o.orderTotal AS 'Total', 
						o.orderItemsSubtotal AS 'Items Subtotal',
						o.orderItemsTax AS 'Items Tax',
						o.orderItemsTotal AS 'Items Total',
						o.orderShippingSubtotal AS 'Shipping Subtotal',
						o.orderShippingTax AS 'Shipping Tax',
						o.orderShippingTotal AS 'Shipping Total',
						o.orderDiscountsTotal AS 'Discounts Total',
						o.orderSubtotal AS 'Subtotal',
						o.orderTaxTotal AS 'Tax Total',
						o.orderItemsRefunded AS 'Items Refunded',
						o.orderTaxRefunded AS 'Tax Refunded',
						o.orderShippingRefunded AS 'Shipping Refunded',
						o.orderTotalRefunded AS 'Total Refunded',
						o.orderTaxID AS 'Customer Tax ID',
						o.orderShippingWeight AS 'Shipping Weight',
						o.orderShippingTaxRate AS 'Tax Rate',
						c.customerFirstName AS 'First Name',
						c.customerLastName AS 'Last Name',
						c.customerEmail AS 'Email',
						pr.promoTitle AS 'Promotion' 
						FROM $orders_table 
							$join_customers
							$join_currencies
							$join_promos
						WHERE $date_range AND $status
						ORDER BY o.orderCreated ASC";

				break;

			case 'orderitems':
				$sql = "SELECT 
							oi.orderID AS 'Order ID',
							o.orderCreated AS 'Date UTC',
							oi.itemQty AS 'Quantity',
							p.sku AS 'SKU',
							p.title AS 'Title',
							p.productVariantDesc AS 'Variant',
							cur.currencyCode AS 'Currency',
							oi.itemPrice AS 'Price',
							oi.itemTax AS 'Tax',
							oi.itemTotal AS 'Total',
							oi.itemTaxRate AS 'Tax rate'
						FROM $items_table
							$join_orders
							$join_products
							$join_currencies
						WHERE $date_range AND $status
						ORDER BY o.orderCreated ASC, p.sku ASC";
 				break;

 			case 'addresses':
 				$sql = "SELECT 
							o.orderID AS 'Order ID',
							o.orderCreated AS 'Date UTC',
							'shipping' AS 'Type',
							c.customerEmail AS 'Email', 
							a.addressDynamicFields AS 'json'
						FROM $orders_table
							$join_customers
							$join_shipping_adr
							$join_countries
						WHERE $date_range AND $status

						UNION 

						SELECT 
							o.orderID AS 'Order ID',
							o.orderCreated AS 'Date UTC',
							'billing' AS 'Type',
							c.customerEmail AS 'Email', 
							a.addressDynamicFields AS 'json'
						FROM $orders_table
							$join_customers
							$join_billing_adr
							$join_countries
						WHERE $date_range AND $status

						ORDER BY `Date UTC` ASC, `Order ID` ASC";
 				break;

		}

		return $sql;
	}


	public function return_instance() { return $this; }

	public function set_null_id() {}

	public function squirrel() {}

	public function itemID() {}

	public function id() {}

	public function ready_to_log_resources() { return false; }
}