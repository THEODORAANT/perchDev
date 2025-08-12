<?php

class PerchAnnouncement_Runtime
{
	private static $instance;

	private $api                  = null;

	private $announcementID           = null;

	


	public static function fetch()
	{
		if (!isset(self::$instance)) self::$instance = new PerchAnnouncement_Runtime;
        return self::$instance;
	}

	public function __construct()
	{
		$this->api = new PerchAPI(1.0, 'perch_announcements');

	}






}
