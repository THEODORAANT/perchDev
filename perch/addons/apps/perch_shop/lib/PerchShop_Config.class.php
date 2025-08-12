<?php

class PerchShop_Config
{
	static $config = false;

	public static function get($opt, $subopt=false)
	{
		$config = self::get_config();

		if (isset($config[$opt])) {
			if ($subopt) {
				if (isset($config[$opt][$subopt])) {
					return $config[$opt][$subopt];
				}else{
					return false;
				}
			}
			return $config[$opt];
		}

		return false;
	}

	public static function get_config()
	{
		if (self::$config!=false) return self::$config;

		self::$config = include PerchUtil::file_path(PERCH_PATH.'/config/shop.php');

		return self::$config;
	}

}
