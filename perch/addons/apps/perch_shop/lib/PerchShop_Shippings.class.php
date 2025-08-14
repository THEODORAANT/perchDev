<?php

class PerchShop_Shippings extends PerchShop_Factory
{
	public $api_method             = 'shipping';
	public $api_list_method        = 'shipping';
	public $singular_classname     = 'PerchShop_Shipping';
	public $static_fields          = ['shippingTitle', 'shippingOrder'];
	public $remote_fields          = ['title', 'slug', 'company', 'status', 'price_min', 'price_max', 'weight_min', 'weight_max', 'description', 'tax_band', 'price'];
	
	protected $table               = 'shop_shippings';
	protected $pk                  = 'shippingID';
	protected $index_table         = 'shop_admin_index';
	protected $master_template	   = 'shop/shippings/shipping.html';
	
	protected $default_sort_column = 'shippingOrder';
	protected $created_date_column = 'shippingCreated';

	protected $event_prefix = 'shop.shipping';

	private $customerID = false;

	public function find_options_for_cart($Currency, $Totaliser, $Zone,$cart_product_categories)
	{
		$Totaliser->calculate($Currency);

		$shippings = $this->get_all_flat();

		$options = array_filter($shippings, function($shipping) use ($Currency, $Totaliser, $Zone,$cart_product_categories) {
			

			// We have a Zone, but price for Zone is not set
			if ($Zone instanceof PerchShop_ShippingZone) {
				if (!in_array($Zone->id(), $shipping['price']['zones'])) {
					PerchUtil::debug('Rejecting shipping method ‘'.$shipping['title'].'’ because: no price available for chosen shipping zone');
					return false;	
				}
			}

                        if (is_array($cart_product_categories) && count($cart_product_categories) > 0) {

                                if (isset($shipping['category']) && is_array($shipping['category']) && count($shipping['category'])) {
                                        $commonValues = array_intersect($cart_product_categories, $shipping['category']);
                                        if (empty($commonValues)) return false;
                                }
                        }


			// Cart weight is less than min shipping weight
			if (isset($shipping['weight_min']) && $shipping['weight_min']!='') {
				if ($Totaliser->weight && ($Totaliser->weight < $shipping['weight_min'])) {
					PerchUtil::debug('Rejecting shipping method ‘'.$shipping['title'].'’ because: cart weight is less than min shipping weight');
					return false;	
				}
			}

			// Cart weight is greater than the max
			if (isset($shipping['weight_max']) && $shipping['weight_max']!='') {
				if ($Totaliser->weight && ($Totaliser->weight > $shipping['weight_max'])) {
					PerchUtil::debug('Rejecting shipping method ‘'.$shipping['title'].'’ because: cart weight is greater than the max');
					return false;	
				}
			}
			
			// Cart width is greater than the max
			if (isset($shipping['width_max']) && $shipping['width_max']!='') {
				if ($Totaliser->width && ($Totaliser->width > $shipping['width_max'])) {
					PerchUtil::debug('Rejecting shipping method ‘'.$shipping['title'].'’ because: cart width is greater than the max');
					return false;	
				}
			}
			
			// Cart height is greater than the max
			if (isset($shipping['height_max']) && $shipping['height_max']!='') {
				if ($Totaliser->height && ($Totaliser->height > $shipping['height_max'])) {
					PerchUtil::debug('Rejecting shipping method ‘'.$shipping['title'].'’ because: cart height is greater than the max');
					return false;	
				}
			}

			// Cart depth is greater than the max
			if (isset($shipping['depth_max']) && $shipping['depth_max']!='') {
				if ($Totaliser->depth && ($Totaliser->depth > $shipping['depth_max'])) {
					PerchUtil::debug('Rejecting shipping method ‘'.$shipping['title'].'’ because: cart depth is greater than the max');
					return false;
				}
			}			

			// Cart value is less than shipping minimum
			if (isset($shipping['price_min'][$Currency->id()]) && $shipping['price_min'][$Currency->id()]!='') {
				if ($Totaliser->grand_total < $shipping['price_min'][$Currency->id()]) {
					PerchUtil::debug('Rejecting shipping method ‘'.$shipping['title'].'’ because: cart value is less than minimum for this shipping type');
					PerchUtil::debug('Cart value: '.$Totaliser->grand_total.'. Shipping min price: '.$shipping['price_min'][$Currency->id()]);
					return false;	
				}
			}
			
			// Cart value is greater than shipping max
			if (isset($shipping['price_max'][$Currency->id()]) && $shipping['price_max'][$Currency->id()]!='') {
				if ($Totaliser->grand_total > $shipping['price_max'][$Currency->id()]) {
					PerchUtil::debug('Rejecting shipping method ‘'.$shipping['title'].'’ because: cart value is greater than maximum for this shipping type');
					return false;	
				}
			}
			
			PerchUtil::debug('Qualifying shipping method ‘'.$shipping['title'].'’');
			return true;
		});

		return $options;
	}


	private function get_all_flat()
	{
		$sql = 'SELECT * FROM '.$this->table.' WHERE shippingDeleted IS NULL ORDER BY shippingOrder ASC';
		$shippings = $this->return_instances($this->db->get_rows($sql));

		$out = [];

		if (PerchUtil::count($shippings)) {
			foreach($shippings as $Shipping) {
				$tmp = $Shipping->to_array();
				if ($tmp['status']=='1') {
					$out[] = $tmp;
				}
			}
		}
		return $out;
	}

	public function get_custom_with_ids($ids, $opts)
	{
		$ids = array_map("intval", $ids);
		return $this->get_filtered_listing($opts, function(PerchQuery $Query) use ($ids){
			$Query->where[] = 'shippingID IN ('.$this->db->implode_for_sql_in($ids).')';
			return $Query;
		});
	}

	public function get_in_set($ids)
	{
		$ids = array_map("intval", $ids);
		$sql = 'SELECT * FROM '.$this->table.' WHERE shippingID IN ('.$this->db->implode_for_sql_in($ids).')';
		return $this->return_instances($this->db->get_rows($sql));
	}

	public function get_zero_prices(PerchShop_Currency $Currency)
	{
		$data = [];

		$data['shipping_method']				= null;
		$data['shipping_without_tax']           = $Currency->format_numeric(0);
		$data['shipping_without_tax_formatted'] = $Currency->format_display(0);
		$data['shipping_tax']                   = $Currency->format_numeric(0);
		$data['shipping_tax_formatted']         = $Currency->format_display(0);
		$data['shipping_tax_rate']              = 0;
		$data['shipping_with_tax']              = $Currency->format_numeric(0);
		$data['shipping_with_tax_formatted']    = $Currency->format_display(0);
		$data['shipping_id'] 					= null;

        ksort($data);

        return $data;
	}

}
