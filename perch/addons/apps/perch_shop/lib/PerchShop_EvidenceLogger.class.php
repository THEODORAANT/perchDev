<?php

class PerchShop_EvidenceLogger
{

	public static function log_for_order($Event)
	{
		if ($Event->args[0] == 'paid') {
			PerchShop_EvidenceLogger::log_card_address($Event);
			PerchShop_EvidenceLogger::log_customer_address($Event);
		}
	}

	public static function log_ip_address($Event)
	{
		$API = new PerchAPI(1.0, 'perch_shop');

		if ($Event && $Event->subject) {
			
			$TaxExhibits = new PerchShop_TaxExhibits($API);

			$locationID = null;
			$countryID  = null;

			$ip_address = PerchUtil::get_client_ip();

			if (defined('PERCH_SHOP_ASSISTANT') && PERCH_SHOP_ASSISTANT) {
				list($locationID, $countryID) = PerchShopAssistant::geocode_ip_address($ip_address);
			}

			$TaxExhibits->log(
					$Event->subject->id(),
					'IP_ADDRESS',
					'Environment',
					$ip_address,
					$locationID,
					$countryID
				);
		}


	}

	public static function log_card_address($Event)
	{
	
		$Order = $Event->subject;

		if ($Order) {
			$API = new PerchAPI(1.0, 'perch_shop');

			$Gateway = PerchShop_Gateways::get($Order->orderGateway());
			$address = $Gateway->get_card_address($Order);

			if ($address) {

				$TaxExhibits = new PerchShop_TaxExhibits($API);
				$locationID = null;
				$countryID  = null;
				$address_detail = 'Unknown';

				PerchUtil::debug($address, 'notice');

				if (isset($address['country']) && $address['country']) {
					$Countries = new PerchShop_Countries($API);
					$Country = $Countries->get_one_by('iso2', $address['country']);
					$address_detail = $address['country'];

					if ($Country) {
						$countryID = $Country->id();

						$Locations = new PerchShop_TaxLocations($API);
						$Location = $Locations->find_matching($Country->id(), null);

						if ($Location) {
							$locationID = $Location->id();
						}
					}
				}

				$TaxExhibits->log(
						$Event->subject->id(),
						'CARD_ADDRESS',
						$Gateway->omnipay_name,
						$address_detail,
						$locationID,
						$countryID
					);
			}		
		}
	}

	public static function log_customer_address($Event)
	{
		$Order = $Event->subject;

		if ($Order) {
			$API = new PerchAPI(1.0, 'perch_shop');
			$TaxExhibits = new PerchShop_TaxExhibits($API);
			$Addresses = new PerchShop_Addresses($API);

			$Billing = $Addresses->find($Order->orderBillingAddress());

			if (!$Billing) return false;

			$Locations = new PerchShop_TaxLocations($API);
			$Location = $Locations->find_matching($Billing->countryID(), $Billing->regionID());
			if ($Location) {
				$locationID = $Location->id();
			}else{
				$locationID = null;
			}

			$TaxExhibits->log(
						$Order->id(),
						'BILL_ADDRESS',
						'Customer',
						$Billing->linearise(),
						$locationID,
						$Billing->countryID()
					);

			$Shipping = $Addresses->find($Order->orderShippingAddress());

			$Location = $Locations->find_matching($Shipping->countryID(), $Shipping->regionID());
			if ($Location) {
				$locationID = $Location->id();
			}else{
				$locationID = null;
			}

			$TaxExhibits->log(
						$Order->id(),
						'SHIP_ADDRESS',
						'Customer',
						$Shipping->linearise(),
						$locationID,
						$Shipping->countryID()
					);

		}
	}
}
