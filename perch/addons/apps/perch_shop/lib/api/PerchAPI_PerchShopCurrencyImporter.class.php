<?php

class PerchAPI_PerchShopCurrencyImporter extends PerchAPI_ContentImporter
{
	protected function setup()
	{
		$this->set_factory(new PerchShop_Currencies($this->api));

		$Template = $this->api->get('Template');
		$Template->set('shop/currencies/currency', 'shop');
		$this->set_template($Template);
	}

}