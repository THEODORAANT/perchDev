<?php

class PerchDB {
	
	static private $instance;
	
	static public $driver = '';

	public static function fetch($config=null)
	{	    
        if (!isset(self::$instance)) {
            self::$instance = new PerchDB_MySQL($config);
        }

        return self::$instance;
	}
}
