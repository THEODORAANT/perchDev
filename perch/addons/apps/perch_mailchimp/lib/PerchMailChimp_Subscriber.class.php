<?php

class PerchMailChimp_Subscriber extends PerchAPI_Base
{
	protected $factory_classname = 'PerchMailChimp_Subscribers';
	protected $table             = 'mailchimp_subscribers';
	protected $pk                = 'subscriberID';

	protected $modified_date_column = 'subscriberUpdated';

	public function update_subscription($listMailChimpID, $subStatus)
	{
		$Lists = new PerchMailChimp_Lists($this->api);
		$List = $Lists->get_one_by('listMailChimpID', $listMailChimpID);

		if (!is_object($List)) {
			return false;
		}

		$Subscriptions = new PerchMailChimp_Subscriptions($this->api);
		$Subscription = $Subscriptions->find_subscription($List, $this);

		if (is_object($Subscription)) {
			$Subscription->update([
				'subStatus' => $subStatus,
				'subUpdated' => date('Y-m-d H:i:s'),
			]);

			return true;
		}
		return false;
	}

}