<?php

define('PERCH_RUNWAY', true);

class PerchShop_Base
{
    protected $details;
    public $api;

    public function __construct($details = [])
    {
        $this->details = $details;
        $this->api     = null;
    }

    public function get($val)
    {
        return $this->details[$val] ?? false;
    }
}

class PerchShop_TaxGroup
{
    private $id;
    private $rateType;

    public function __construct($id, $rateType)
    {
        $this->id       = $id;
        $this->rateType = $rateType;
    }

    public function id()
    {
        return $this->id;
    }

    public function groupTaxRate()
    {
        return $this->rateType;
    }
}

class PerchShop_TaxRates
{
    public function __construct($api)
    {
    }

    public function get_rate_for_location($groupID, $locationID)
    {
        if ($locationID == 100) {
            return 20; // UK home rate
        }

        return 0; // Non-UK
    }
}

class PerchShop_TaxLocation
{
    private $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function id()
    {
        return $this->id;
    }
}

class PerchShop_Currency
{
    private $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function id()
    {
        return $this->id;
    }

    public function format_numeric($number)
    {
        return number_format($number, 2, '.', '');
    }

    public function format_display($number)
    {
        return '£' . number_format($number, 2, '.', '');
    }
}

require_once __DIR__ . '/../lib/PerchShop_CartTotaliser.class.php';
require_once __DIR__ . '/../lib/PerchShop_Product.class.php';

class TestProduct extends PerchShop_Product
{
    public function __construct($details)
    {
        $this->details = $details;
        $this->api     = null;
    }

    public function get_tax_group()
    {
        return new PerchShop_TaxGroup(1, 'buyer');
    }

    public function get_property($prop, PerchShop_Product $Parent=null)
    {
        return $this->details[$prop] ?? false;
    }
}

$Product             = new TestProduct(['price' => [1 => 120]]);
$CustomerTaxLocation = new PerchShop_TaxLocation(200); // non-UK buyer
$HomeTaxLocation     = new PerchShop_TaxLocation(100); // UK home
$Currency            = new PerchShop_Currency(1);
$Totaliser           = new PerchShop_CartTotaliser();

$price = $Product->get_prices(1, 'standard', 'inc', $CustomerTaxLocation, $HomeTaxLocation, $Currency, $Totaliser);

if ($price['price_with_tax'] !== '100.00') {
    throw new Exception('Inclusive price incorrect');
}

if ($price['tax'] !== '0.00') {
    throw new Exception('Tax amount incorrect');
}

if ($price['tax_formatted'] !== '£0.00') {
    throw new Exception('Formatted tax incorrect');
}

echo "Test passed\n";

