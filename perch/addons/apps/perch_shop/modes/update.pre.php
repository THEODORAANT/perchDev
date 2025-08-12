<?php
		// Prevent running directly:
    if (!defined('PERCH_DB_PREFIX')) exit;

    $sql = file_get_contents(__DIR__.'/../db.sql');
    $sql .= file_get_contents(__DIR__.'/../db_updates.sql');
	$sql = str_replace('__PREFIX__', PERCH_DB_PREFIX, $sql);

	$DB = PerchDB::fetch();

    $statements = explode(';', $sql);
    foreach($statements as $statement) {
        $statement = trim($statement);
        if ($statement!='') $DB->execute($statement);
    }

    $Products = new PerchShop_Products($API);
    $products = $Products->all();

    if (PerchUtil::count($products)) {
        foreach($products as $Product) {
            $status = $Product->get('status');
            if ($status != '1' && $status != '0') {
                $status = '1';
            }
            $Product->intelliupdate(['status'=>$status]);
        }
    }


    $API = new PerchAPI(1.0, 'perch_shop');
    $UserPrivileges = $API->get('UserPrivileges');
    $UserPrivileges->create_privilege('perch_shop', 'Access Shop');
    $UserPrivileges->create_privilege('perch_shop.brands.create', 'Create new brands');
    $UserPrivileges->create_privilege('perch_shop.brands.edit', 'Edit brands');
    $UserPrivileges->create_privilege('perch_shop.brands.delete', 'Delete brands');
    $UserPrivileges->create_privilege('perch_shop.currencies.create', 'Create new currencies');
    $UserPrivileges->create_privilege('perch_shop.currencies.delete', 'Delete currencies');
    $UserPrivileges->create_privilege('perch_shop.currencies.edit', 'Edit currencies');
    $UserPrivileges->create_privilege('perch_shop.customers.create', 'Create new customers');
    $UserPrivileges->create_privilege('perch_shop.customers.delete', 'Delete customers');
    $UserPrivileges->create_privilege('perch_shop.customers.edit', 'Edit customers');
    $UserPrivileges->create_privilege('perch_shop.email.create', 'Create new emails');
    $UserPrivileges->create_privilege('perch_shop.emails.delete', 'Delete emails');
    $UserPrivileges->create_privilege('perch_shop.emails.edit', 'Edit emails');
    $UserPrivileges->create_privilege('perch_shop.options.create', 'Create new product options');
    $UserPrivileges->create_privilege('perch_shop.options.edit', 'Edit product options');
    $UserPrivileges->create_privilege('perch_shop.orders', 'Access orders');
    $UserPrivileges->create_privilege('perch_shop.orders.edit', 'Edit orders');
    $UserPrivileges->create_privilege('perch_shop.products', 'Access Products');
    $UserPrivileges->create_privilege('perch_shop.products.create', 'Create new products');
    $UserPrivileges->create_privilege('perch_shop.products.edit', 'Edit products');
    $UserPrivileges->create_privilege('perch_shop.products.delete', 'Delete products');
    $UserPrivileges->create_privilege('perch_shop.promos.create', 'Create new promotions');
    $UserPrivileges->create_privilege('perch_shop.promos.edit', 'Edit promotions');
    $UserPrivileges->create_privilege('perch_shop.promos.delete', 'Delete promotions');
    $UserPrivileges->create_privilege('perch_shop.shippings.create', 'Create new shipping methods');
    $UserPrivileges->create_privilege('perch_shop.shippings.delete', 'Delete shipping methods');
    $UserPrivileges->create_privilege('perch_shop.shippings.edit', 'Edit shipping methods');
    $UserPrivileges->create_privilege('perch_shop.statuses.create', 'Create new order statuses');
    $UserPrivileges->create_privilege('perch_shop.statuses.delete', 'Delete order statuses');
    $UserPrivileges->create_privilege('perch_shop.statuses.edit', 'Edit order statuses');
    $UserPrivileges->create_privilege('perch_shop.taxbands.create', 'Create new tax bands');
    $UserPrivileges->create_privilege('perch_shop.taxbands.delete', 'Delete tax bands');
    $UserPrivileges->create_privilege('perch_shop.taxbands.edit', 'Edit tax bands');
    $UserPrivileges->create_privilege('perch_shop.taxgroups.create', 'Create new tax groups');
    $UserPrivileges->create_privilege('perch_shop.taxgroups.delete', 'Delete tax groups');
    $UserPrivileges->create_privilege('perch_shop.taxgroups.edit', 'Edit tax groups');
    $UserPrivileges->create_privilege('perch_shop.taxlocations.create', 'Create new tax locations');
    $UserPrivileges->create_privilege('perch_shop.taxlocations.delete', 'Delete tax locations');
    $UserPrivileges->create_privilege('perch_shop.taxlocations.edit', 'Edit tax locations');


    $Settings->set('perch_shop_update', PERCH_SHOP_VERSION);