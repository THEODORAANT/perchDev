<?php

class PerchAPI_PerchShopBrandImporter extends PerchAPI_ContentImporter
{
	protected function setup()
	{
		$this->set_factory(new PerchShop_Brands($this->api));

		$Template = $this->api->get('Template');
		$Template->set('shop/brands/brand', 'shop');
		$this->set_template($Template);
	}

}