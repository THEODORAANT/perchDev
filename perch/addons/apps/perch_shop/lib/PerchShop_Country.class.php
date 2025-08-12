<?php

class PerchShop_Country extends PerchAPI_Base
{
	protected $table  = 'shop_countries';
    protected $pk     = 'countryID';
    protected $event_prefix = 'shop.country';

    public function title()
    {
    	return $this->details['country'];
    }
}