<?php
    include(__DIR__.'/_version.php');


    if ($CurrentUser->logged_in() && $CurrentUser->has_priv('perch_shop')) {
        $this->register_app('perch_shop', 'Shop', 1, 'Ecommerce', PERCH_SHOP_VERSION);
        $this->require_version('perch_shop', '3.1');

        $this->add_setting('perch_shop_price_tax_mode', 'Prices entered as', 'select', 'exc', [
                ['label'=>'Tax exclusive', 'value'=>'exc'],
                ['label'=>'Tax inclusive', 'value'=>'inc'],
            ]);
        if (PERCH_RUNWAY) {
            $this->add_setting('perch_shop_trade_price_tax_mode', 'Trade prices entered as', 'select', 'exc', [
                    ['label'=>'Tax exclusive', 'value'=>'exc'],
                    ['label'=>'Tax inclusive', 'value'=>'inc'],
                ]);
        }
        $this->add_setting('perch_shop_site_url', 'Live site URL', 'text', 'https://');
        $this->add_setting('perch_shop_product_url', 'Product URL', 'text', '/shop/products/{slug}');
        $this->add_setting('perch_shop_default_currency', 'Default currency', 'PerchShop_Currencies::get_settings_select_list', '');
        $this->add_setting('perch_shop_reporting_currency', 'Reporting currency', 'PerchShop_Currencies::get_settings_select_list', '');
        $this->add_setting('perch_shop_invoice_number_format', 'Invoice number format', 'text', "Invoice%d");

        PerchSystem::register_admin_search_handler('PerchShop_SearchHandler');
        PerchSystem::register_template_handler('PerchShop_Template');
    }

    include(__DIR__.'/lib/vendor/autoload.php');

    spl_autoload_register(function($class_name){
        if (strpos($class_name, 'PerchShopGateway')===0) {
            $path = PERCH_PATH.'/addons/apps/perch_shop/lib/gateways/'.$class_name.'.class.php';
            if (file_exists($path)){
                include($path);
                return true;
            }
            return false;
        }
        if (strpos($class_name, 'PerchShop_')===0) {
            include(PERCH_PATH.'/addons/apps/perch_shop/lib/'.$class_name.'.class.php');
            return true;
        }
        return false;
    });

    // Fieldtypes
    include_once(__DIR__.'/fieldtypes.php');

    // event listeners
    include_once(__DIR__.'/events.php');