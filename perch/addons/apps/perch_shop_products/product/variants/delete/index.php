<?php
	$delete_priv = 'perch_shop.products.delete';
	$factory = 'PerchShop_Products';
	$return_path = '/product/variants/?id=';
	$title = 'Delete Product';

	$delete_callback = function($Factory) {

		if (PerchUtil::get('id')) {
			$Item = $Factory->find(PerchUtil::get('id'));
			if ($Item) {
				return '/product/variants/?id='.$Item->parentID();
			}
		}
		return '/';
	};

	include('../../../_default_delete.php');