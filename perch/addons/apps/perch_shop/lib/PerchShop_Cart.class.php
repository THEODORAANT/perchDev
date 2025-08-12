<?php

class PerchShop_Cart extends PerchShop_Factory
{
	public $static_fields          = [];
	
	protected $table               = 'shop_cart';
	protected $pk                  = 'cartID';
	public $singular_classname     = 'PerchShop_Cart';
	protected $index_table         = '';
	protected $master_template     = 'shop/cart/cart.html';
	
	protected $default_sort_column = '';
	protected $created_date_column = '';
	
	private $item_table            = '';
	
	private $cart_id               = false;
	
	private $cached_cart           = null;
	private $shipping_options      = null;
	private $shipping_zone         = null;

	protected $event_prefix = 'shop.cart';

	public function __construct($api=false)
	{
		parent::__construct($api);
		$this->item_table = PERCH_DB_PREFIX.'shop_cart_items';
	}

	public function init()
	{
		if ($this->cart_id && $this->cart_id!='') {
			return $this->cart_id;
		}

		#$Session = new PerchShop_Session;

		if (PerchShop_Session::is_set('cartID') && PerchShop_Session::get('cartID')!='') {
			$this->cart_id = PerchShop_Session::get('cartID');
		}else{
			$this->cart_id = $this->_create_new_cart();
			PerchShop_Session::set('cartID', $this->cart_id);
			PerchShop_Session::set('cartPricing', 'standard');
		}

		return $this->cart_id;
	}

	public function get_cart_id()
	{
		if (!$this->cart_id) return $this->init();
		return $this->cart_id;
	}

	public function get_cart_field($field)
	{
		$sql = 'SELECT * FROM '.$this->table.' WHERE cartID='.$this->db->pdb((int)$this->cart_id);
		$row = $this->db->get_row($sql);
		if (isset($row[$field])) return $row[$field];
		return false;
	}

	public function add_to_cart($product, $qty=1, $replace=false)
	{
		if (is_array($product)) {
			$data    = $product;
			$product = $data['id'];
			$qty     = $data['qty'];
		}

		$existing_id = false;

		if ($replace) {
			$sql = 'DELETE FROM '.$this->item_table.' WHERE cartID='.$this->db->pdb($this->cart_id).' AND productID='.$this->db->pdb((int)$product);
			$this->db->execute($sql);
		}else{
			// already in the cart?
			$sql = 'SELECT itemID, itemQty FROM '.$this->item_table.' WHERE cartID='.$this->db->pdb((int)$this->cart_id).' AND productID='.$this->db->pdb((int)$product);
			$row = $this->db->get_row($sql);
			if (PerchUtil::count($row)) {
				$existing_id = (int)$row['itemID'];
				$existing_qty = (int)$row['itemQty'];
			}
		}

		if ($existing_id) {
			$new_qty = $existing_qty + (int)$qty;
		}else{
			$new_qty = $qty;
		}

		$available_stock = $this->_get_product_stock_level($product);

		if ($new_qty > $available_stock) {
			PerchUtil::debug('Limiting based on stock.', 'notice');
			$new_qty = $available_stock;
		}

		if ($existing_id) {

			if ($new_qty==0) {
				$sql = 'DELETE FROM '.$this->item_table.' WHERE cartID='.$this->db->pdb($this->cart_id).' AND productID='.$this->db->pdb((int)$product);
				$this->db->execute($sql);
			}else{
				$this->db->update($this->item_table, [
				'itemQty' => $new_qty,
				], 'itemID', $existing_id);	
			}
			

		}else{
			if ($new_qty>0) {
				$this->db->insert($this->item_table, [
					'productID' => $product,
					'itemQty' => $new_qty,
					'cartID' => $this->cart_id,
					]);
			}
		}

		$this->recalculate_summary_data();

		return true;
	}

	public function set_location($locationID)
	{
		$this->update(['locationID'=>$locationID]);
		$this->recalculate_summary_data();
	}

	public function set_member($memberID)
	{
		$this->update(['memberID'=>$memberID]);
	}

	public function set_customer($customerID)
	{
		$this->customerID = $customerID;
		$this->update(['customerID'=>$customerID]);
	}

	public function set_currency($currencyID)
	{
		$this->update(['currencyID'=>$currencyID]);
		$this->recalculate_summary_data();
	}

	public function set_shipping($shippingID)
	{
		$this->update(['shippingID'=>$shippingID]);
		$this->recalculate_summary_data();
	}

	public function set_pricing_mode($mode='standard')
	{
		$this->update(['cartPricing'=>$mode]);
		$this->recalculate_summary_data();
	}

	public function set_addresses($billingAddress='default', $shippingAddress='default')
	{
		$this->update([
			'billingAddress'  => $billingAddress,
			'shippingAddress' => $shippingAddress
			]);



		$this->recalculate_summary_data();
	}

	public function get_addresses()
	{
		$sql = 'SELECT billingAddress, shippingAddress FROM '.PERCH_DB_PREFIX.'shop_cart WHERE billingAddress IS NOT NULL AND cartID='.(int)$this->cart_id;
		$row = $this->db->get_row($sql);

		return $row;
	}

	public function get_shipping_zone($Customer, $HomeTaxLocation)
	{
		$Zones = new PerchShop_ShippingZones($this->api);

		if ($Customer) {
			$set_adr = $this->get_addresses();
			if ($set_adr && isset($set_adr['shippingAddress'])) {
				$Addresses = new PerchShop_Addresses($this->api);
				$ShippingAddress = $Addresses->find_for_customer($Customer->id(), $set_adr['shippingAddress']);
				
				if ($ShippingAddress) {
					
            		$Zone = $Zones->find_matching($ShippingAddress->countryID());

            		if ($Zone) {
            			return $Zone;	
            		}
				}
			}	
		}

		$Zone = $Zones->find_matching($HomeTaxLocation->countryID());
		if ($Zone) {
			return $Zone;
		}
		
		return null;
	}

	public function get_pricing_mode()
	{
		#$Session = new PerchShop_Session;
		if (PerchShop_Session::is_set('cartPricing')) {
			return PerchShop_Session::get('cartPricing');
		}

		$sql = 'SELECT cartPricing FROM '.PERCH_DB_PREFIX.'shop_cart WHERE cartID='.(int)$this->cart_id;
		$pricing = $this->db->get_value($sql);

		PerchShop_Session::set('cartPricing', $pricing);

		return $pricing;
	}

	public function update($data)
	{
		$this->init();
		$this->db->update($this->table, $data, 'cartID', $this->cart_id);
	}

	public function recalculate_summary_data()
	{
		#PerchUtil::debug(debug_backtrace()[1]['function']);
		$this->init();

		$data = [];

		// we're being told to recalculate, so always clear the cache
		$this->cached_cart = null;

		// prices
		$cart_data = $this->calculate_cart();

		$data['cartTotalItems']      = $cart_data['item_count'];
		$data['cartTotalProducts']   = $cart_data['product_count'];
		$data['cartTotalWithTax']    = $cart_data['grand_total'];
		$data['cartTotalWithoutTax'] = $cart_data['total_items'];

		$this->update($data);

	}

	public function set_discount_code($code)
	{
		$code = filter_var($code, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		$code = trim(strtoupper($code));

		$this->update(['cartDiscountCode'=>$code]);
		$this->recalculate_summary_data();

		return false;
	}

	public function destroy()
	{
		$this->db->delete($this->table, 'cartID', $this->cart_id);
		$this->cart_id = false;
		PerchSession::delete('cartID');
		$this->calculate_cart();

	}

	public function get_cart($opts, PerchShop_Cache $Cache)
	{
		$data = false;

		if ($Cache->exists('cart.contents')) {
			$data = unserialize($Cache->get('cart.contents'));
			PerchUtil::debug('Using cart from cache.');
		}

		if (!$data) {
			$data = $this->calculate_cart();
		}

		if (PerchUtil::count($data)) {
			$result = $this->template_cart($data, $opts);	
			$Cache->set('cart.contents', serialize($data));
			return $result;
		}

	}

	public function get_cart_for_api($opts, PerchShop_Cache $Cache)
	{
		return $this->format_result_for_api($this->calculate_cart(), $opts);
	}

	public function get_shipping_options($opts, PerchShop_Cache $Cache)
	{
		$shipping_opts = false;
		$shipping_zone = false;

		if (!$shipping_opts && $this->shipping_options) {
			$shipping_opts = $this->shipping_options;
		}

		if (!$shipping_opts && $Cache->exists('cart.shipping_opts')) {
			$shipping_opts = unserialize($Cache->get('cart.shipping_opts'));
			PerchUtil::debug('Using shipping opts from cache.');
		}

		if (!$shipping_opts) {
			$this->calculate_cart();
			$shipping_opts  = $this->shipping_options;
		}

		if (!$shipping_zone && $this->shipping_zone) {
			$shipping_zone = $this->shipping_zone;
		}

		if (!$shipping_zone && $Cache->exists('cart.shipping_zone')) {
			$shipping_zone = $Cache->get('cart.shipping_zone');
			$this->shipping_zone = $shipping_zone;
			PerchUtil::debug('Using shipping zone from cache.');
		}

		if ($shipping_opts) {
			$Shippings = new PerchShop_Shippings($this->api);
			PerchSystem::set_var('customer_zone_id', $shipping_zone);
			$out = $Shippings->get_custom_with_ids($shipping_opts, $opts);
			$Cache->set('cart.shipping_opts', serialize($shipping_opts));
			$Cache->set('cart.shipping_zone', $shipping_zone);
			return $out;
		}

		return false;
	}

	/**
	 * For the select list when the customer picks a shipping method
	 */
	public function get_shipping_list_options($opts, PerchShop_Cache $Cache)
	{
		$shipping_opts = false;

		if (!$shipping_opts && $this->shipping_options) {
			$shipping_opts = $this->shipping_options;
		}

		if (!$shipping_opts && $Cache->exists('cart.shipping_opts')) {
			$shipping_opts = unserialize($Cache->get('cart.shipping_opts'));
			PerchUtil::debug('Using shipping opts from cache.');
		}

		if (!$shipping_opts) {
			$this->calculate_cart();
			$shipping_opts  = $this->shipping_options;
		}

		if ($shipping_opts) {
			$Shippings = new PerchShop_Shippings($this->api);
			$rows = $Shippings->get_in_set($shipping_opts);
			$out = [];

			if (PerchUtil::count($rows)) {
				foreach($rows as $Shipping) {
					$out[] = $Shipping->get('title').'|'.$Shipping->id();
				}
				return implode(',', $out);
			}

			return false;


			$Cache->set('cart.shipping_opts', serialize($shipping_opts));
			return $out;
		}

		return false;
	}

	public function get_cart_val($property='total', $opts=[], PerchShop_Cache $Cache=null)
	{
		$data = false;

		if ($Cache->exists('cart.contents')) {
			$data = unserialize($Cache->get('cart.contents'));
		}

		if (!$data) {
			$data = $this->calculate_cart();
			$Cache->set('cart.contents', serialize($data));
		}

		if (PerchUtil::count($data)) {

			if (isset($data[$property])) {
				return $data[$property];
			}
		}

		return false;
	}

	public function get_value_of_shipped_goods()
	{
		// TODO


		return 0;
	}

	public function update_from_form($SubmittedForm)
	{
		$data = $SubmittedForm->data;

		$changes = [];

		if (PerchUtil::count($data)) {
			foreach($data as $field=>$qty) {
				if (substr($field, 0, 4)=='qty:') {
					$identifier = str_replace('qty:', '', $field);
					$changes[$identifier] = (int)$qty;
				}
			}

			// Delete (needs to be a distinct loop after qty as both will be set in most cases)
			foreach($data as $field=>$qty) {
				if (substr($field, 0, 4)=='del:') {
					$identifier = str_replace('del:', '', $field);
					$changes[$identifier] = 0;
				}
			}
		}

		if (PerchUtil::count($changes)) {

			foreach($changes as $id=>$qty) {
				if ($qty==0) {
					// delete from cart
					$sql = 'DELETE FROM '.$this->item_table.' WHERE cartID='.$this->db->pdb((int)$this->cart_id).' AND productID='.$this->db->pdb((int)$id);
					$this->db->execute($sql);
				}else{

					$available_stock = $this->_get_product_stock_level($id);
					if ($qty > $available_stock) $qty = $available_stock;

					// update qty
					$sql = 'UPDATE '.$this->item_table.' SET itemQty='.$this->db->pdb((int)$qty).' WHERE cartID='.$this->db->pdb($this->cart_id).' AND productID='.$this->db->pdb((int)$id);
					$this->db->execute($sql);
				}
			}
		}

		$this->recalculate_summary_data();

		// Remove cart form from $_POST so that forced qty changes (due to stock) are reflected correctly
		$SubmittedForm->clear_from_post_env();

		return true;
	}

	public function stash_data($data)
	{
		$productID = null;

		if (isset($data['product'])) $productID = $data['product'];

		$this->db->insert(PERCH_DB_PREFIX.'shop_cart_data', [
			'cartID' => $this->get_cart_id(),
			'productID' => $productID,
			'cartData' => PerchUtil::json_safe_encode($data),
			]);
	}

	public function add_order_id_to_stashed_data($order_id)
	{
		$this->db->update(PERCH_DB_PREFIX.'shop_cart_data', [
			'orderID' => $order_id
			], 'cartID', $this->get_cart_id());
	}

	public function set_property($property, $value)
	{
		$this->set_properties([$property=>$value]);
	}

	public function set_properties(array $properties)
	{
		$current_props = $this->get_properties();
		$new_props = array_merge($current_props, $properties);

		$this->update([
			'cartProperties' => PerchUtil::json_safe_encode($new_props)
		]);
	}

	public function get_property($property)
	{
		$props = $this->get_properties();
		if (isset($props[$property])) {
			return $props[$property];
		}

		return null;
	}

	public function get_properties()
	{
		$sql = 'SELECT cartProperties FROM '.$this->table.' WHERE cartID='.$this->db->pdb((int)$this->cart_id);
		$json = $this->db->get_value($sql);
        $jsondecode =PerchUtil::json_safe_decode($json, true);
		if (count($jsondecode)) {
			return $jsondecode ;
		}

		return [];
	}

	private function template_cart($result, $opts)
	{
		$data = $this->format_result_for_template($result, $opts);
		if (isset($data['items'])) {
			$PerchShop_Runtime = PerchShop_Runtime::fetch();
			$PerchShop_Runtime->cart_items = $data['items'];
		}

		if ($opts['skip-template']==false) {

			$Template = $this->api->get('Template');
			$Template->set($opts['template'], $this->namespace);
			$html = $Template->render($data);
			return $Template->apply_runtime_post_processing($html);
		}

		return $result;
	}

	private function format_result_for_template($result, $opts)
	{
		if (PerchUtil::count($result)) {

			if (isset($result['items'])) {
				foreach($result['items'] as &$item) {
					$item['identifier'] = $item['id'];
					$item['quantity'] = $item['qty'];

					$item = array_merge($item, $item['Product']->to_array());

					if (isset($item['productVariantDesc'])) {
						$item['variant_desc'] = $item['productVariantDesc'];
					}

					unset($item['Product']);
				}
			}

		}

		return $result;
	}

	private function _create_new_cart()
	{
		$member_id   = null;
		$location_id = null;
		$currency_id = null;

		if (perch_member_logged_in()) {
			$member_id = perch_member_get('memberID');
			// TODO : find member location default and currecy prefs.
		}

		if (!$currency_id) {
			$Settings = PerchSettings::fetch();
			$currency_id = (int)$Settings->get('perch_shop_default_currency')->val();
		}

		if (!$location_id) {
			$location_id = $this->get_home_tax_location_id();
		}

		return $this->db->insert($this->table, [
			'memberID'       => $member_id,
			'locationID'     => $location_id,
			'currencyID'     => $currency_id,
			'cartPricing'    => 'standard',
			'cartProperties' => '[]',
			]);
	}

	private function get_home_tax_location_id()
	{
		return $this->db->get_value('SELECT locationID FROM '.PERCH_DB_PREFIX.'shop_tax_locations WHERE locationIsHome=1 LIMIT 1');
	}


	private function _get_product_stock_level($productID)
	{
		$Products = new PerchShop_Products($this->api);
		$Product = $Products->find((int)$productID);

		if ($Product) {

			// weigh up the limiting factors and return the lowest
			$opts = [];

			// stock level
			$opts[] = $Product->get_stock_level();
			

			// 'max in cart' limit
			$max_in_cart = (int)$Product->get('max_in_cart');

			if ($max_in_cart > 0) {
				$opts[] = $max_in_cart;
			}


			// return whichever is the most constraining
			return min($opts);
		}

		return 0;
	}


	public function calculate_cart()
	{
		if ($this->cached_cart) return $this->cached_cart;

		PerchUtil::mark('Calculating cart');

		$data = [];

		$data['discount_code']  = null;
		$data['promotions'] 	= [];

		$discount_code = $this->get_cart_field('cartDiscountCode');
		if ($discount_code) {
			$data['discount_code'] = $discount_code;
		}

		$Items        = new PerchShop_CartItems($this->api);
		$Products     = new PerchShop_Products($this->api);
		$TaxLocations = new PerchShop_TaxLocations($this->api);
		$TaxGroups    = new PerchShop_TaxGroups($this->api);
		$Currencies   = new PerchShop_Currencies($this->api);
		$Shippings    = new PerchShop_Shippings($this->api);
		$Customers    = new PerchShop_Customers($this->api);
		$PromoEngine  = new PerchShop_PromotionsEngine($this->api);

		$Settings = PerchSettings::fetch();

		// Get the cart data
		$cart = $this->db->get_row('SELECT * FROM '.$this->table.' WHERE cartID='.(int)$this->cart_id);

		// Get the tax location we're working with
		$CustomerTaxLocation = $TaxLocations->find((int)$cart['locationID']);
		$HomeTaxLocation 	 = $TaxLocations->find((int)$this->get_home_tax_location_id());

		if (!$CustomerTaxLocation && $HomeTaxLocation) {
			$CustomerTaxLocation = $HomeTaxLocation;
		}

		// Find the customer
		$customer_pays_tax = true;

		$Customer = null;

		if ($cart['memberID']) {
			$Customer = $Customers->find_by_memberID((int)$cart['memberID']);
			if ($Customer) {
				$customer_pays_tax = $Customer->pays_tax($CustomerTaxLocation, $HomeTaxLocation);
			}else{
				$Customer = null;
			}
		}

		// Find the currency
		$Currency = $Currencies->find((int)$cart['currencyID']);
		if (!$Currency) {
			$Currency = $Currencies->get_default();

			if (!$Currency) {
				die('No default currency set.');
			}
		}

		// item counts
		$sql = 'SELECT COUNT(productID) AS product_count, SUM(itemQty) AS item_count FROM '.PERCH_DB_PREFIX.'shop_cart_items WHERE cartID='.(int)$this->cart_id;
		$row = $this->db->get_row($sql);
		if (is_array($row)) {
			$data = array_merge($data, $row);
		}

		// Tax mode
        $tax_mode = $Settings->get('perch_shop_price_tax_mode')->val();
        if (PERCH_RUNWAY && $cart['cartPricing']=='trade') {
        	$tax_mode = $Settings->get('perch_shop_trade_price_tax_mode')->val();
        }

        PerchUtil::debug("Tax mode: $tax_mode");

		// Items
		$items = $Items->get_for_cart((int)$this->cart_id);

		// Create a totaliser to keep track of totals
		$Totaliser = new PerchShop_CartTotaliser;

		// Create a mock totaliser for getting prices without affecting the cart
		$MockTotaliser = new PerchShop_CartTotaliser;
		$cart_product_categories=array();

		if (PerchUtil::count($items)) {
			$data['items'] = [];
			foreach($items as $Item) {

				// Find the product
				$Product = $Products->find((int)$Item->productID());


				$cart_product_categories=array_merge($cart_product_categories,$Product->category());

				$item    = $Product->get_prices($Item->itemQty(), $cart['cartPricing'], $tax_mode, $CustomerTaxLocation, $HomeTaxLocation, $Currency, $Totaliser, $customer_pays_tax);

				// Add the sale prices for reference
				// Used by sale price promos
				if ($cart['cartPricing'] != 'sale') {
					$item['ref_sale_prices'] = $Product->get_prices($Item->itemQty(), 'sale', $tax_mode, $CustomerTaxLocation, $HomeTaxLocation, $Currency, $MockTotaliser, $customer_pays_tax);
				}

				$item['weight']  = $Product->get_weight_and_totalise_shipping($Item->itemQty(), $Totaliser);
				$item['sku']     = $Product->sku();
				$item['title']   = $Product->title();
				$item['Product'] = $Product;
				
				$item['qty']     = $Item->itemQty();
				$item['id']      = $Item->productID();

				$data['items'][] = $item;
			}
		}

		// Work out shipping options
		$Shipping = null;
		$this->shipping_options = [];

		if ($Totaliser->shippable_items > 0) {

			$ShippingZone = $this->get_shipping_zone($Customer, $HomeTaxLocation);
			
			if ($ShippingZone) {
				$this->shipping_zone = $ShippingZone->id();	
			}


			$shipping_options = $Shippings->find_options_for_cart($Currency, $Totaliser, $ShippingZone,$cart_product_categories);
		
			if (PerchUtil::count($shipping_options)) {

				// cache
				foreach($shipping_options as $opt) {
					$this->shipping_options[] = $opt['shippingID'];
				}

				if ($cart['shippingID']) {
					// use the existing shipping option (find it in the set)
					foreach($shipping_options as $shipping) {
						if ($shipping['shippingID'] == $cart['shippingID']) {
							// only select it if it's in the valid set.
							// (that's why we're searching for it in this loop.)
							$Shipping = $Shippings->find($cart['shippingID']);
						}
					}
				}

				// Do we have a Shipping? If not, take the first. It should be the cheapest.
				if (!$Shipping) {
					$opt = array_shift($shipping_options);
					$Shipping = $Shippings->find($opt['shippingID']);	
				}
			}
			if ($Shipping) {
				$shipping_prices = $Shipping->get_prices($tax_mode, $CustomerTaxLocation, $HomeTaxLocation, $ShippingZone, $Currency, $Totaliser, $customer_pays_tax);
				if (is_array($shipping_prices)) {
					$data = array_merge($data, $shipping_prices);
				}
			}
		}else{
			$data = array_merge($data, $Shippings->get_zero_prices($Currency));
		}

		// Calculate any discounts
		$data = $PromoEngine->calculate_discounts($data, $Totaliser, $Currency, $tax_mode, $Customer, $Shipping);
		
		$data = array_merge($data, $Totaliser->to_array($Currency));

		// Add currency data for good measure
		$data['currency_id']     = $Currency->id();
		$data['currency_code']   = $Currency->currencyCode();
		$data['currency_name']   = $Currency->currencyTitle();
		$data['currency_symbol'] = $Currency->currencySymbol();


		$this->cached_cart = $data;
		return $data;
	}


	private function format_result_for_api($result, $opts)
	{
		if (PerchUtil::count($result['items'])) {

		
			$to_delete = ['_variant_opts', 'options', 'tax_group'];

			$result = $this->_format_field($result, 'Product', 'shop/products/product', $to_delete);

			foreach($result['items'] as &$item) {

				if ($item['ref_sale_prices']) {
					unset($item['ref_sale_prices']);
				}
			}

		}

		if ($result['promotions']) {
			unset($result['promotions']);
		}

		return $result;
	}

	private function _format_field($result, $id, $template, $to_delete)
	{
		$Template = $this->api->get('Template');
		$Template->set($template, 'shop');
		$field_type_map = $Template->get_field_type_map('shop');

		foreach($result['items'] as &$item) {	
			if ($item[$id]) {

				$data = $item[$id]->to_array_for_api();


				foreach($data as $key => &$field) {

					if (in_array($key, $to_delete)) {
						unset($data[$key]);
						continue;
					}

                    if (array_key_exists($key, $field_type_map)) {
                        $field = $field_type_map[$key]->get_api_value($field);
                        continue;
                    }

                    if (is_array($field) && isset($field['processed'])) {
                        $field = $field['processed'];
                    }
                    if (is_array($field) && isset($field['_default'])) {
                        $field = $field['_default'];
                    }
                }
                unset($item[$id]);
				$item[strtolower($id)] = $data;
			}
		}

		return $result;
	}

}
