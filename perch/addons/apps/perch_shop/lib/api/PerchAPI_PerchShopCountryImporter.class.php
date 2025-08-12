<?php

class PerchAPI_PerchShopCountryImporter extends PerchAPI_ContentImporter
{
	protected function setup()
	{
		$this->set_factory(new PerchShop_Countries($this->api));

		$Template = $this->api->get('Template');
		$Template->set('shop/countries/country', 'shop');
		$this->set_template($Template);
	}

}