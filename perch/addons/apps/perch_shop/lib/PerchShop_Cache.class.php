<?php

class PerchShop_Cache
{
    static private $instance;
    private $cache = array();

    private static $disabled = false;

    public static function fetch()
    {
        if (!isset(self::$instance)) {
            $c = __CLASS__;
            self::$instance = new $c;
        }

         return self::$instance;
    }

    public function __construct()
    {
    	if (PerchShop_Session::is_set('shop_cache')) {
    		$this->cache = PerchShop_Session::get('shop_cache');
    	}
    }

    public function __destruct()
    {
    	PerchShop_Session::set('shop_cache', $this->cache);
    }

    public function write()
    {
        PerchShop_Session::set('shop_cache', $this->cache);
    }

    public function exists($key)
    {
        return array_key_exists($key, $this->cache);
    }

    public function get($key)
    {
        if (array_key_exists($key, $this->cache)) {
            return $this->cache[$key];
        }

        return false;
    }

    public function set($key, $value)
    {
        $this->cache[$key] = $value;
    }

    public function expire_like($prefix)
    {
    	$len = strlen($prefix);
    	$new = [];
    	foreach($this->cache as $key=>$val) {
    		if (substr($key, 0, $len)!=$prefix) {
    			$new[$key] = $val;
    		}
    	}
    	$this->cache = $new;
        $this->write();
    }

    public function expire_all()
    {
    	$this->cache = [];
    }
}
