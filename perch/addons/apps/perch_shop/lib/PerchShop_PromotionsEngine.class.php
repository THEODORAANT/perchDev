<?php

class PerchShop_PromotionsEngine
{
	private $api = null;

	public function __construct($api)
	{
		$this->api = $api;
	}


	public function calculate_discounts(array $data, PerchShop_CartTotaliser &$Totaliser, PerchShop_Currency $Currency, $tax_mode, PerchShop_Customer $Customer=null, PerchShop_Shipping $Shipping=null)
	{
		// do we have items in the cart?
		if (!isset($data['items']) || !PerchUtil::count($data['items'])) {
			return $data;
		}

		// get all the promos
		$promos = $this->get_promotions();

		// if we have promos
		if (PerchUtil::count($promos)) {

			$only_apply_if_persistent = false;

			// run through each and see which apply
			foreach($promos as $Promotion) {

				// Did a previous promo terminate? If so, only apply if persistent
				if ($only_apply_if_persistent) {
					if (!$Promotion->get('persistent')) {
						continue;
					}
				}


				// Does this promo apply to this cart?
				// Each condition falls through. If a condition is found that means the promo doesn't apply, we `continue` the loop.

				$action = $Promotion->get('action');

				// Discount code
				$code = $Promotion->get('discount_code');
				if ($code) {
					// promo uses a code - so does the cart have it?
					if (strtoupper($data['discount_code'])!=strtoupper($code)) {
						PerchUtil::debug('Promo ('.$Promotion->title().') skipped: discount code not applied.');
						continue;
					}
				}

				// Use-counts
				$max_uses 		= $Promotion->get('max_uses');
				$customer_uses 	= $Promotion->get('customer_uses');

				if ($max_uses || $customer_uses) {
					// usage is limited

					if ($max_uses) {
						$use_count = $Promotion->get_use_count();
						if ($use_count>=(int)$max_uses) {
							PerchUtil::debug('Promo ('.$Promotion->title().') skipped: has hit maximum number of uses.');
							continue;
						}
					}

					if ($customer_uses && !is_null($Customer)) {
						$use_count = $Promotion->get_use_count($Customer->id());
						if ($use_count>=(int)$customer_uses) {
							PerchUtil::debug('Promo ('.$Promotion->title().') skipped: has hit maximum number of uses for this customer.');
							continue;
						}
					}
				}

				// Tigger values
				$applies_to_shipping = $Promotion->get('apply_to_shipping');
				$trigger_value 		 = $Promotion->get('trigger_value');

				if (isset($trigger_value[$Currency->id()])) {
					// trigger values have been set, so check if we hit them

					// Find the cart value based on the conditions of the promo
					if ($applies_to_shipping  && $action != 'free_ship') {
						// don't factor in shipping if it's a free-shipping promo. That'd be insane.
						if ($tax_mode=='inc') {
							$compare_value = ($Totaliser->items + $Totaliser->tax + $Totaliser->shipping + $Totaliser->shipping_tax);
						}else{
							$compare_value = ($Totaliser->items + $Totaliser->shipping);
						}
					}else{
						if ($tax_mode=='inc') {
							$compare_value = ($Totaliser->items + $Totaliser->tax);
						}else{
							$compare_value = ($Totaliser->items);
						}
					}

					if ($compare_value < floatval($trigger_value[$Currency->id()])) {
						// not enough in the cart to trigger the promo
						PerchUtil::debug('Promo ('.$Promotion->title().') skipped: not enough in the cart to trigger the minimum value.');
						continue;
					}
				}

				// Qualifing shipping method
				if ($action=='free_ship' && !is_null($Shipping)) {
					$allowable_shipping_methods = $Promotion->get('shipping_methods');
					if (!in_array($Shipping->id(), $allowable_shipping_methods)) {
						continue;
					}
				}


				
				/********************/
				/* Promo qualifies! */
				/********************/

				PerchUtil::debug('Promo ('.$Promotion->title().') qualifies!');
				#PerchUtil::debug($data);

				// discount by amount or percent?
				$discount_percent = (int)$Promotion->get('amount_percent');
				$discount_amount  = 0;

				if (!$discount_percent) {
					$currency_amounts = $Promotion->get('amount');
					if (isset($currency_amounts[$Currency->id()])) {
						$discount_amount = floatval($currency_amounts[$Currency->id()]);
					}
				}

				// find discount maximum
				$currency_max_discounts = $Promotion->get('max_discount');
				$max_discount = INF;
				if (isset($currency_max_discounts[$Currency->id()]) && (float)$currency_max_discounts[$Currency->id()]>0) {
					$max_discount = floatval($currency_max_discounts[$Currency->id()]);
				}

				// find product categories to match against
				$promo_categories = $Promotion->get('categories');

				
				$data['promotions'][] = $Promotion;

				switch($action) {

					case 'free_ship':
						PerchUtil::debug('This is a free shipping promo');
						$Totaliser->add_to_shipping_discounts($data['shipping_without_tax'], $data['shipping_tax_rate']);
						$Totaliser->add_to_shipping_tax_discounts($data['shipping_tax'], $data['shipping_tax_rate']);
						break;

					case 'discount_by_percent':
						PerchUtil::debug('Percentage discount promo');
						$discounted = 0;
						if (PerchUtil::count($data['items'])) {
							$new_items = [];
							foreach($data['items'] as $item) {
								if ($discounted < $max_discount) {
									$price    = (float)$item['total_without_tax'];
									$tax      = (float)$item['total_tax'];
									$tax_rate = $item['tax_rate'];

									$discount 	  = ($price/100) * $discount_percent;
									$tax_discount = ($tax/100) * $discount_percent;

									if (($discounted+$discount) <= $max_discount) {
										$Totaliser->add_to_item_discounts($discount, $tax_rate);
										$Totaliser->add_to_tax_discounts($tax_discount, $tax_rate);

										if (!isset($item['discount'])) $item['discount'] = 0;
										if (!isset($item['tax_discount'])) $item['tax_discount'] = 0;

										$item['discount'] += $discount;
										$item['tax_discount'] += $tax_discount;

										$discounted += $discount;
									}

								}else{
									PerchUtil::debug('Hit maximum discount.');
								}
								$new_items[] = $item;
							}
							$data['items'] = $new_items;
						}
						break;

					case 'discount_by_fixed':
						PerchUtil::debug('Fixed amount discount promo');
						$discounted = 0;
						if (PerchUtil::count($data['items'])) {
							$new_items = [];
							foreach($data['items'] as $item) {
								if ($discounted < $max_discount) {
									$price    = (float)$item['total_without_tax'];
									$tax      = (float)$item['total_tax'];
									$tax_rate = $item['tax_rate'];

									if ($price >= $discount_amount) {
										$discount_for_this_item = $discount_amount;
									}else{
										$discount_for_this_item = $price;
									}

									$discount           = $discount_for_this_item;
									$new_price          = $price - $discount_for_this_item;
									$multiplier         = 1 + ($tax_rate/100);
									$new_price_with_tax = $new_price * $multiplier;
									$new_tax            = $new_price_with_tax - $new_price;
									$tax_discount       = $tax - $new_tax;

									$Totaliser->add_to_item_discounts($discount, $tax_rate);
									$Totaliser->add_to_tax_discounts($tax_discount, $tax_rate);

									if (!isset($item['discount'])) $item['discount'] = 0;
									if (!isset($item['tax_discount'])) $item['tax_discount'] = 0;

									$item['discount'] += $discount;
									$item['tax_discount'] += $tax_discount;

									$discounted += $discount;

								}else{
									PerchUtil::debug('Hit maximum discount.');
								}
								$new_items[] = $item;
							}
							$data['items'] = $new_items;
						}
						break;

					case 'use_sale_price':
						PerchUtil::debug('Use sale price promo');
						$discounted = 0;
						if (PerchUtil::count($data['items'])) {
							$new_items = [];
							foreach($data['items'] as $item) {
								if ($discounted < $max_discount) {
									if ($this->categories_intersect($promo_categories, $item['Product']->get_property('category'))) {

										if (array_key_exists('ref_sale_prices', $item) && PerchUtil::count($item['ref_sale_prices'])) {
											
											$price    = (float)$item['total_without_tax'];
											$tax      = (float)$item['total_tax'];
											$tax_rate = $item['tax_rate'];

											$sale_price = (float)$item['ref_sale_prices']['total_without_tax'];

											if ($price >= $sale_price) {
												$discount_for_this_item = ($price - $sale_price);
											}else{
												$discount_for_this_item = $price;
											}

											$discount           = $discount_for_this_item;
											$new_price          = $sale_price;
											$multiplier         = 1 + ($tax_rate/100);
											$new_price_with_tax = $new_price * $multiplier;
											$new_tax            = $new_price_with_tax - $new_price;
											$tax_discount       = $tax - $new_tax;

											$Totaliser->add_to_item_discounts($discount, $tax_rate);
											$Totaliser->add_to_tax_discounts($tax_discount, $tax_rate);

											if (!isset($item['discount'])) $item['discount'] = 0;
											if (!isset($item['tax_discount'])) $item['tax_discount'] = 0;

											$item['discount'] += $discount;
											$item['tax_discount'] += $tax_discount;

											$discounted += $discount;
										
										}
									
									}

								}else{
									PerchUtil::debug('Hit maximum discount.');
								}
								$new_items[] = $item;
							}
							$data['items'] = $new_items;
						}
						break;


				}

				// Is this promo terminating?
				if ($Promotion->get('terminating')) {
					$only_apply_if_persistent = true;
				}

			}
		}



		return $data;
	}


	private function categories_intersect($promo_categories, $product_categories)
	{
		if (!PERCH_RUNWAY) {
			return true;
		}

		// if no promo categories are set, then all good because promo is not restricted by categories
		if (!PerchUtil::count($promo_categories)) {
			PerchUtil::debug('Promo is not restricted by category');
			return true;
		}

		// If there are no product categories, then it's false because it won't match
		if (!PerchUtil::count($product_categories)) {
			PerchUtil::debug($product_categories, 'notice');
			PerchUtil::debug('Promo is restricted by category, and product has no categories set');
			return false;
		}

		// At this point we should have categories on both sides. Do they intersect?
		$result = array_intersect($promo_categories, $product_categories);
		if (count($result)) {
			PerchUtil::debug('Promo is restricted by category, and product has matching categories');
			return true;	
		} 

		PerchUtil::debug('Promo is restricted by category, but product has not matching categories');
		return false;
	}

	private function get_promotions()
	{
		$Promotions = new PerchShop_Promotions($this->api);
		return $Promotions->get_currently_active();
	}
}