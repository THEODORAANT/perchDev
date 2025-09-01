<?php
	include(__DIR__.'/fieldtypes.php');
	include(__DIR__.'/lib/vendor/autoload.php');
	//PerchShop_Session::commence();

    if (!function_exists('perch_members_init')) {
        die('Please ensure that the Members app is installed and appears before the Shop app in your config/apps.php file.');
    }

	spl_autoload_register(function($class_name){
		if (strpos($class_name, 'PerchShopGateway')===0) {
            $path = PERCH_PATH.'/addons/apps/perch_shop/lib/gateways/'.$class_name.'.class.php';
            if (file_exists($path)){
                include($path);
                return true;
            }
            return false;
        }
        if (strpos($class_name, 'API_PerchShop')>0) {
            include(__DIR__.'/lib/api/'.$class_name.'.class.php');
            return true;
        }
		if (strpos($class_name, 'PerchShop_')===0) {
			include(__DIR__.'/lib/'.$class_name.'.class.php');
			return true;
		}
		return false;
	});

	PerchSystem::register_template_handler('PerchShop_Template');
	PerchSystem::register_search_handler('PerchShop_SearchHandler');

	include(__DIR__.'/runtime/forms.php');
	include(__DIR__.'/runtime/addresses.php');
	include(__DIR__.'/runtime/brands.php');
	include(__DIR__.'/runtime/products.php');
	include(__DIR__.'/runtime/cart.php');
	include(__DIR__.'/runtime/orders.php');
	include(__DIR__.'/runtime/email.php');
        include(__DIR__.'/runtime/gateways.php');
        include(__DIR__.'/runtime/customers.php');
        include(__DIR__.'/runtime/events.php');
        include(__DIR__.'/runtime/packages.php');
        include(__DIR__.'/events.php');

