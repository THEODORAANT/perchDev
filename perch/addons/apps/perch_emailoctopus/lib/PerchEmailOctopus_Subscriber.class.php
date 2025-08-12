<?php

class PerchEmailOctopus_Subscriber extends PerchAPI_Base
{
	protected $factory_classname = 'PerchEmailOctopus_Subscribers';
	protected $table             = 'emailoctopus_subscribers';
	protected $pk                = 'subscriberID';

	protected $modified_date_column = 'subscriberUpdated';

	/*public function update_subscription($listperch_emailoctopusID, $subStatus)
	{
		$Lists = new PerchEmailOctopus_Lists($this->api);
		$List = $Lists->get_one_by('listperch_emailoctopusID', $listMailChimpID);

		if (!is_object($List)) {
			return false;
		}

		$Subscriptions = new PerchEmailOctopus_Subscriptions($this->api);
		$Subscription = $Subscriptions->find_subscription($List, $this);

		if (is_object($Subscription)) {
			$Subscription->update([
				'subStatus' => $subStatus,
				'subUpdated' => date('Y-m-d H:i:s'),
			]);

			return true;
		}
		return false;
	}*/

}
