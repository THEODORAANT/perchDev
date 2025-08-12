<?php

class PerchShop_VariantFactory extends PerchAPI_Factory
{
	public $singular_classname     = 'PerchShop_Product';

	protected $table               = 'shop_variants';
	protected $pk                  = 'productID';
	protected $master_template	   = 'shop/products/product.html';

	protected $default_sort_column = 'title';
	protected $created_date_column = 'productCreated';

	public function generate_for_product($productID)
	{
		PerchUtil::debug('Generating variants');

		$productID = (int)$productID;

		$Products     = new PerchShop_Products($this->api);
		$Options      = new PerchShop_Options($this->api);
		$OptionValues = new PerchShop_OptionValues($this->api);

		$Product = $Products->find($productID);

		if ($Product) {

			$Template   = $this->api->get('Template');
			$Template->set('shop/products/variant.html', 'shop');

			$data = [];

			// Get all the options
			$options = $Options->get_for_product($productID);
			if (PerchUtil::count($options)) {
				foreach($options as $Option) {
					$row = [];
					$values = $Option->get_values_for_product($productID);
					if (PerchUtil::count($values)) {
						foreach($values as $Value) {
							$row[] = [
								'optionID' => $Value->optionID(),
								'valueID' => $Value->valueID(),
								'sku' => $Value->valueSKUCode(),
								'title' => $Value->valueTitle(),
							];
						}
					}
					$data[] = $row;
				}
			}

			$combos = $this->possible_combos($data);

			$i = 1;

			if (PerchUtil::count($combos)) {
				foreach($combos as $combo) {
					$variants = [];
					$sku = [];
					$title = [];
					$sku[] = $Product->sku();
					if (PerchUtil::count($combo)) {
						foreach($combo as $option) {
							$sku[] = $option['sku'];
							$title[] = $option['title'];
							$variants[] = [
								'optionID'  => $option['optionID'],
								'valueID'   => $option['valueID'],
							];
						}
					}

					$sku = implode('-', $sku);
					#PerchUtil::debug($sku);

					// Does a product exist with this SKU?
					$Variant = $Products->get_one_by('sku', $sku);

					if (!is_object($Variant)) {
						$Variant = $Products->create([
								'sku'                  => $sku,
								'parentID'             => $Product->id(),
								'title'                => $Product->title(),
								'productVariantDesc'   => implode(', ', $title),
								'productOrder'         => $i,
								'stock_level'          => $Product->stock_level(),
								'productHasVariants'   => '0',
								'productStockOnParent' => $Product->productStockOnParent(),
								'productDynamicFields' => '{}', //$Product->productDynamicFields(),
								'productTemplate'      => $Product->productTemplate(),
								'productCreated'       => date('Y-m-d H:i:s'),
							]);

						if (is_object($Variant) && PerchUtil::count($variants)) {
							foreach($variants as $variant) {
								$variant['productID'] = $Variant->id();
								$this->db->insert(PERCH_DB_PREFIX.'shop_variants', $variant);
							}

							$Variant->index($Template);	
						}
						$i++;
					} else {
						// variant exists. reindex for good luck
						// $Variant->index($Template);	
					}
				} 

				$Product->update([
					'productHasVariants' => '1',
				]);
			}

			#PerchUtil::debug($combos, 'success');

		}
	}

	private function possible_combos($options, $prefix=array()) 
	{
	    $result = array();
	    $option = array_shift($options);
	    foreach($option as $optionValue) {
			$prefix[] = $optionValue;

	        if ($options) {
	            $result = array_merge($result, $this->possible_combos($options, $prefix));
	        }else{
	            $result[] = $prefix;
	        }
	        array_pop($prefix);
	    }
	    return $result;
	}

}