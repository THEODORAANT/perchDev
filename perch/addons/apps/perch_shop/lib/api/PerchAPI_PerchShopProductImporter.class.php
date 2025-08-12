<?php

class PerchAPI_PerchShopProductImporter extends PerchAPI_ContentImporter
{

	protected function setup()
	{
		$this->set_factory(new PerchShop_Products($this->api));

		$Template = $this->api->get('Template');
		$Template->set('shop/products/product', 'shop');
		$this->set_template($Template);
	}

	protected function validate_input($data)
	{
		parent::validate_input($data);

		if (isset($data['sku'])) {
			$Factory = $this->get_active_factory();
			
			$existing = $Factory->get_one_by('sku', $data['sku']);
			if ($existing) {
				throw new \Exception('Duplicate SKU: '. $data['sku']);
			}
		}

	}

}