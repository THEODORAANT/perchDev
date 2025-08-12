<?php
	include(__DIR__.'/../perch_shop/_version.php');

if ($CurrentUser->logged_in() && $CurrentUser->has_priv('perch_shop.products')) {
    $this->register_app('perch_shop_products', 'Products', 1, 'Ecommerce', PERCH_SHOP_VERSION);
    $this->require_version('perch_shop_products', '2.0');

}