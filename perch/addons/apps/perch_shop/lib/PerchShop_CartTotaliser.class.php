<?php

class PerchShop_CartTotaliser
{
	public $items                  = 0;
	public $shipping               = 0;
	public $tax                    = 0;
	public $shipping_tax           = 0;
	public $discounts              = 0;
	public $discounts_tax          = 0;
	public $shipping_discounts     = 0;
	public $shipping_discounts_tax = 0;
	public $weight                 = 0;
	public $width                  = 0;
	public $height                 = 0;
	public $depth                  = 0;
	
	public $shippable_items = 0;
	public $tax_rate_totals = [];

	public $grand_total;

	public function add_to_shippable_items($count=1)
	{
		$this->shippable_items += $count;
	}

	public function add_to_items($val, $rate)
	{
		$this->items += $val;

		$this->add_to_tax_totals_as_items($rate, $val);

	}

	public function add_to_item_discounts($val, $rate)
	{
		//$this->items = $this->items - $val;
		$this->discounts += $val;

		$this->add_to_tax_totals_as_items($rate, -$val);

	}

	public function add_to_shipping($val, $rate)
	{
		$this->shipping += $val;

		$this->add_to_tax_totals_as_items($rate, $val);

	}

	public function add_to_shipping_discounts($val, $rate)
	{
		$this->shipping = $this->shipping - $val;
		$this->shipping_discounts += $val;

		$this->add_to_tax_totals_as_items($rate, -$val);

	}

	public function add_to_tax($val, $rate)
	{
		$this->tax += $val;

		$this->add_to_tax_totals_as_tax($rate, $val);
	}

	public function add_to_tax_discounts($val, $rate)
	{
		$this->tax = $this->tax - $val;
		$this->discounts_tax += $val;

		$this->add_to_tax_totals_as_tax($rate, -$val);
	}

	public function add_to_shipping_tax($val, $rate)
	{
		$this->shipping_tax += $val;

		$this->add_to_tax_totals_as_tax($rate, $val);
	}

	public function add_to_shipping_tax_discounts($val, $rate)
	{
		$this->shipping_tax = $this->shipping_tax - $val;
		$this->shipping_discounts_tax += $val;

		$this->add_to_tax_totals_as_tax($rate, -$val);
	}

	public function add_to_discounts($val)
	{
		$this->discounts += $val;
	}

	public function add_to_weight($val)
	{
		$this->weight += $val;
	}

	public function add_to_dimensions($w, $h, $d)
	{
		if ($w > $this->width) 	$this->width  = $w;
		if ($h > $this->height) $this->height = $h;
		if ($d > $this->depth) 	$this->depth  = $d;
	}

	public function calculate()
	{
		$this->grand_total = (($this->items + $this->shipping) - $this->discounts) + ($this->tax + $this->shipping_tax);
	}

	public function to_array(PerchShop_Currency $Currency, PerchShop_Order $Order=null)
	{
		$this->calculate();

		$out                                              = [];
		$out['total_items']                               = $Currency->format_numeric($this->items);
		$out['total_items_formatted']                     = $Currency->format_display($this->items);
		
		$out['total_items_tax']                           = $Currency->format_numeric($this->tax);
		$out['total_items_tax_formatted']                 = $Currency->format_display($this->tax);
		
		$out['total_shipping']                            = $Currency->format_numeric($this->shipping);
		$out['total_shipping_formatted']                  = $Currency->format_display($this->shipping);
		
		$out['total_shipping_tax']                        = $Currency->format_numeric($this->shipping_tax);
		$out['total_shipping_tax_formatted']              = $Currency->format_display($this->shipping_tax);
		
		$out['total_items_with_shipping']                 = $Currency->format_numeric($this->items + $this->shipping);
		$out['total_items_with_shipping_formatted']       = $Currency->format_display($this->items + $this->shipping);
		
		$out['total_tax']                                 = $Currency->format_numeric($this->tax + $this->shipping_tax);
		$out['total_tax_formatted']                       = $Currency->format_display($this->tax + $this->shipping_tax);
		
		$out['total_items_discount']                      = $Currency->format_numeric($this->discounts);
		$out['total_items_discount_formatted']            = $Currency->format_display($this->discounts);
		$out['total_items_tax_discount']                  = $Currency->format_numeric($this->discounts_tax);
		$out['total_items_tax_discount_formatted']        = $Currency->format_display($this->discounts_tax);
		
		$out['total_items_discounted']                    = $Currency->format_numeric($this->items - $this->discounts);
		$out['total_items_discounted_formatted']          = $Currency->format_display($this->items - $this->discounts);
		$out['total_items_discounted_with_tax']           = $Currency->format_numeric(($this->items - $this->discounts)+$this->tax);
		$out['total_items_discounted_with_tax_formatted'] = $Currency->format_display(($this->items - $this->discounts)+$this->tax);
		
		$out['total_shipping_discount']                   = $Currency->format_numeric($this->shipping_discounts);
		$out['total_shipping_discount_formatted']         = $Currency->format_display($this->shipping_discounts);
		$out['total_shipping_tax_discount']               = $Currency->format_numeric($this->shipping_discounts_tax);
		$out['total_shipping_tax_discount_formatted']     = $Currency->format_display($this->shipping_discounts_tax); 
		
		$out['total_tax_discount']                        = $Currency->format_numeric($this->discounts_tax + $this->shipping_discounts_tax);
		$out['total_tax_discount_formatted']              = $Currency->format_display($this->discounts_tax + $this->shipping_discounts_tax);
		
		
		$out['total_discounts']                           = $Currency->format_numeric($this->discounts + $this->shipping_discounts);
		$out['total_discounts_formatted']                 = $Currency->format_display($this->discounts + $this->shipping_discounts);
		
		$out['total_discounts_with_tax']                  = $Currency->format_numeric($this->discounts + $this->shipping_discounts + $this->discounts_tax + $this->shipping_discounts_tax);
		$out['total_discounts_with_tax_formatted']        = $Currency->format_display($this->discounts + $this->shipping_discounts + $this->discounts_tax + $this->shipping_discounts_tax);
		
		
		$out['grand_total']                               = $Currency->format_numeric($this->grand_total);
		$out['grand_total_formatted']                     = $Currency->format_display($this->grand_total);
		
		$out['shipping_weight']                           = $Currency->format_numeric($this->weight);
		
		$out['tax_rate_totals']                           = $this->calculate_tax_rate_totals($Currency, $Order);
		
		return $out;

	}

	private function add_to_tax_totals_as_items($rate, $val)
	{
		$this->add_to_tax_totals($rate, $val, 'items');
	}

	private function add_to_tax_totals_as_tax($rate, $val)
	{
		$this->add_to_tax_totals($rate, $val, 'tax');
	}

	private function add_to_tax_totals($rate, $val, $key)
	{
		$rate = (string)$rate;
		
		if (array_key_exists($rate, $this->tax_rate_totals)) {
			if (!is_array($this->tax_rate_totals[$rate])) {
				$this->tax_rate_totals[$rate] = [];
			}
			if (!isset($this->tax_rate_totals[$rate][$key])) {
				$this->tax_rate_totals[$rate][$key] = 0;
			}

			$this->tax_rate_totals[$rate][$key] += $val;
		}else{
			$this->tax_rate_totals[$rate] = [];
			$this->tax_rate_totals[$rate][$key] = $val;
		}

	}

	private function calculate_tax_rate_totals($Currency, $Order)
	{
		$out = [];

		if (PerchUtil::count($this->tax_rate_totals)) {
			ksort($this->tax_rate_totals);
			foreach($this->tax_rate_totals as $rate=>$data) {
				$exchange_rate_output = 'unknown';
				$exchange_rate = 1;
				$ReportingCurrency = $Currency;

				if ($Order && $Order->orderExchangeRate()) {
					$exchange_rate = (float) $Order->orderExchangeRate();
					$exchange_rate_output = number_format($exchange_rate, 5, '.', '');
					$ReportingCurrency = $Order->get_reporting_currency();

					if (!$ReportingCurrency) $ReportingCurrency = $Currency;
				}

				if (!isset($data['tax'])) 	$data['tax'] = 0;
				if (!isset($data['items'])) $data['items'] = 0;

				$out[] = [
							'exchange_rate'             => $exchange_rate_output,
							'tax_rate'                  => $rate,
							'tax_rate_formatted'        => floatval(number_format($rate, 2, '.', '')).' %',
							'total_tax'                 => $Currency->format_numeric($data['tax']),
							'total_tax_formatted'       => $Currency->format_display($data['tax']),
							'total_value'               => $Currency->format_numeric($data['items']),
							'total_value_formatted'     => $Currency->format_display($data['items']),
							'reporting_tax'             => $ReportingCurrency->format_numeric($data['tax']/$exchange_rate),
							'reporting_tax_formatted'   => $ReportingCurrency->format_display($data['tax']/$exchange_rate),
							'reporting_value'           => $ReportingCurrency->format_numeric($data['items']/$exchange_rate),
							'reporting_value_formatted' => $ReportingCurrency->format_display($data['items']/$exchange_rate),

						];
			}
		}
		return $out;
	}
}