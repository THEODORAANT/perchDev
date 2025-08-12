<?php

class PerchShop_Gateways
{
	public static function get($gatewaySlug)
	{
		$api         = new PerchAPI(1.0, 'perch_shop');
		$classSlug = str_replace('-', '_', $gatewaySlug);
		$classname   = "PerchShopGateway_$classSlug";

		if (class_exists($classname)) {
			return new $classname($api, $gatewaySlug);
		}

		return new PerchShopGateway_default($api, $gatewaySlug);

	}
}