<?php

class PerchShop_TaxExhibit extends PerchShop_Base
{
    protected $table        = 'shop_tax_exhibits';
    protected $pk           = 'exhibitID';

    protected $event_prefix = 'shop.tax';

}