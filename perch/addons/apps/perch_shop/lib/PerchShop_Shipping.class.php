<?php

class PerchShop_Shipping extends PerchShop_Base
{
	protected $factory_classname = 'PerchShop_Shippings';
	protected $table             = 'shop_shippings';
	protected $pk                = 'shippingID';
	protected $index_table       = 'shop_admin_index';

	protected $modified_date_column = 'shippingUpdated';
    public $deleted_date_column  = 'shippingDeleted';

	protected $duplicate_fields  = array('shippingTitle'=>'title', 'shippingSlug'=>'slug');

    protected $event_prefix = 'shop.shipping';

	public function to_array()
	{
		$out = parent::to_array();
        $out['zones'] = [];

        if (isset($out['price']['zones']) && PerchUtil::count($out['price']['zones'])) {
            $Zones = new PerchShop_ShippingZones($this->api);
            $zones = $Zones->find_set($out['price']['zones']);

            if (PerchUtil::count($zones)) {
                foreach($zones as $Zone) {
                    $tmp = $Zone->to_array();
                    if (isset($out['price']['z'.$Zone->id()])) {
                        $tmp['price'] = $out['price']['z'.$Zone->id()];
                    }
                    $out['zones'][] = $tmp;
                }
            }
        }
        
		return $out;
	}

	public function get_prices($price_tax_mode='exc', PerchShop_TaxLocation $CustomerTaxLocation=null, PerchShop_TaxLocation $HomeTaxLocation=null, PerchShop_ShippingZone $Zone=null, PerchShop_Currency $Currency=null, PerchShop_CartTotaliser &$Totaliser=null, $customer_pays_tax=true)
    {
        $prices_tax_inclusive = ($price_tax_mode=='inc');

        $price_field = 'price';

        $prices = $this->get($price_field);

        $data = [];

        if ($prices) {

            // find zone
            if ($Zone && isset($prices['z'.$Zone->id()])) {
                $prices = $prices['z'.$Zone->id()];
                PerchUtil::debug('Using shipping zone: '.$Zone->title());
            }else{
                if (!$Zone) {
                    PerchUtil::debug('No shipping zone', 'error');    
                }

                if ($Zone && !isset($prices['z'.$Zone->id()])) {
                    PerchUtil::debug('No prices for zone: '.$Zone->title(), 'error'); 
                }
                
                return $data;
            }

            $TaxRates  = new PerchShop_TaxRates($this->api);
            $TaxGroups = new PerchShop_TaxGroups($this->api);

            if (isset($prices[$Currency->id()])) {
                $base_price = $prices[$Currency->id()];

                // Whos tax rate do we use?
                if ($this->get('tax_group')) {
		            $TaxGroup = $TaxGroups->find((int)$this->get('tax_group'));
		        }

		        if (!$TaxGroup) {
		        	PerchUtil::debug("Shipping tag group not found.", 'error');
		        	return [];
		        }

                if ($TaxGroup->groupTaxRate()=='buyer') {
                    $TaxLocation = $CustomerTaxLocation;
                }else{
                    $TaxLocation = $HomeTaxLocation;
                }

                // Which rate to charge? Standard, reduced etc
                $tax_rate = $TaxRates->get_rate_for_location((int)$TaxGroup->id(), (int)$TaxLocation->id());

                // Add or remove tax?
                $multiplier = 1 + ($tax_rate/100);

                if ($prices_tax_inclusive) {
                    // remove tax from base price
                    $exclusive_price = $base_price / $multiplier;
                    $inclusive_price = $base_price;
                }else{
                    // add tax to base price
                    $exclusive_price = $base_price;
                    $inclusive_price = $base_price * $multiplier;
                }

                $Totaliser->add_to_shipping($exclusive_price, $tax_rate);

                if ($customer_pays_tax) {
                	$Totaliser->add_to_shipping_tax(($inclusive_price - $exclusive_price), $tax_rate);
                }
               
               	if (!$customer_pays_tax) {
               		$inclusive_price = $exclusive_price;
               	}


                $data['shipping_method']				= $this->title();

				$data['shipping_without_tax']           = $Currency->format_numeric($exclusive_price);
				$data['shipping_without_tax_formatted'] = $Currency->format_display($exclusive_price);
				
				$data['shipping_tax']                   = $Currency->format_numeric($inclusive_price - $exclusive_price);
				$data['shipping_tax_formatted']         = $Currency->format_display($inclusive_price - $exclusive_price);
				
				$data['shipping_tax_rate']              = $tax_rate;
				
				$data['shipping_with_tax']              = $Currency->format_numeric($inclusive_price);
				$data['shipping_with_tax_formatted']    = $Currency->format_display($inclusive_price);

				$data['shipping_id'] 					= $this->id();

                ksort($data);

                return $data;
            }
        }

    }

}
