<?php

class PerchShop_Settings
{
	public static function get($setting)
	{
		$Settings = PerchSettings::fetch();
		$Setting = $Settings->get('perch_shop_'.$setting);
		return $Setting->val();		
	}
}