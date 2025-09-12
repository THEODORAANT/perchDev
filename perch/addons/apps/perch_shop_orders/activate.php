<?php
	$API = new PerchAPI(1.0, 'perch_shop');
	$UserPrivileges = $API->get('UserPrivileges');
	$UserPrivileges->create_privilege('perch_shop.orders', 'Access Orders');
	$UserPrivileges->create_privilege('perch_shop.customers.create', 'Create customers');
	$UserPrivileges->create_privilege('perch_shop.customers.edit', 'Edit customers');
	$UserPrivileges->create_privilege('perch_shop.customers.delete', 'Delete customers');
