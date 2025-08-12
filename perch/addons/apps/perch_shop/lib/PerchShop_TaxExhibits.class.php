<?php

class PerchShop_TaxExhibits extends PerchShop_Factory
{
    protected $table               = 'shop_tax_exhibits';
    protected $pk                  = 'exhibitID';
    protected $singular_classname  = 'PerchShop_TaxExhibit';

    protected $namespace           = 'shop';
    protected $event_prefix        = 'shop.tax';

    protected $default_sort_column = 'exhibitDate';

    public $static_fields          = ['exhibitType', 'exhibitDetail', 'exhibitSource', 'locationID', 'orderID', 'exhibitDate'];

	protected $created_date_column = 'exhibitDate';


	public function log($orderID=0, $type='ADDRESS', $source='', $detail='', $locationID=null, $countryID=null)
    {
    	$this->create([
			'orderID'       => (int)$orderID,
			'exhibitType'   => $type,
			'exhibitDetail' => $detail,
			'exhibitSource' => $source,
			'locationID'    => $locationID,
			'countryID'    => $countryID,
    		]);
    }
}
