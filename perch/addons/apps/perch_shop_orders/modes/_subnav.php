<?php
	PerchUI::set_subnav([
		['page'=>[
						'perch_shop_orders',
						'perch_shop_orders/order',
						'perch_shop_orders/order/edit',
						'perch_shop_orders/order/evidence',
						'perch_shop_orders/export',
						], 
				'label'=>'Orders'],
		['page'=>[
						'perch_shop_orders/customers',
						'perch_shop_orders/customers/edit',
						'perch_shop_orders/customers/delete',
						], 
				'label'=>'Customers']

	], $CurrentUser);