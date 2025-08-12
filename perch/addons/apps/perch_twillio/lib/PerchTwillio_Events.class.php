<?php

class PerchTwillio_Events
{
/*	public static function order_status($Event)
	{
		$Order = $Event->subject;
		$status = $Event->args[0];

		if ($status) {
			$API  = new PerchAPI(1.0, 'perch_shop');
			$OrderStatuses = new PerchShop_OrderStatuses($API);

			$OrderStatus = $OrderStatuses->find_by_key($status);
			##PerchUtil::debug($OrderStatus);

			if ($OrderStatus) {

				// find emails for status
				$Emails = new PerchShop_Emails($API);
				$emails = $Emails->get_for_status($OrderStatus->id());

				if (PerchUtil::count($emails)) {
					foreach($emails as $Email) {
						$Order->send_order_email($Email);
					}
				}
			}
		}else{
			#PerchUtil::debug($Event);
		}
	}*/

	public static function register_member_login($Event)
	{
		$Runtime = PerchTwillio_Runtime::fetch();
		$Runtime->register_member_login($Event);
	}
}
