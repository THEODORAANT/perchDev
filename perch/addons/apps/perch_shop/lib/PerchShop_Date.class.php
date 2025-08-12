<?php

class PerchShop_Date
{
	public static function format($date, $format)
	{
		$Date = new DateTime($date, new DateTimeZone('UTC'));
		$Date->setTimeZone(new DateTimeZone(PERCH_TZ));
		return date($format, strtotime($Date->format('Y-m-d H:i:s')));
	}
}
