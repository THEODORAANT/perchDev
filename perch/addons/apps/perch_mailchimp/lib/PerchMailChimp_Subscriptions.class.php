<?php

class PerchMailChimp_Subscriptions extends PerchMailChimp_Factory
{
	protected $table               = 'mailchimp_subscriptions';
    protected $pk                  = 'subID';
    protected $singular_classname  = 'PerchMailChimp_Subscription';

    protected $namespace           = 'subscription';

    protected $event_prefix        = 'mailchimp.subscription';
	protected $master_template	   = 'mailchimp/subscriptions/subscription.html';
	
	protected $default_sort_column = 'subCreated';
	protected $created_date_column = 'subCreated';


	public function create_from_import(PerchMailChimp_List $List, PerchMailChimp_Subscriber $Subscriber, array $member)
	{
		$Subscription = $this->find_subscription($List, $Subscriber);

		if (is_object($Subscription)) {
			$Subscription->update([
				'subStatus'	 => $member['status'],
				'subRating'  => $member['member_rating'],
				'subUpdated' => ($member['last_changed'] ? date('Y-m-d H:i:s', strtotime($member['last_changed'])) : null),
				]);

		}else{
			$Subscription = $this->create([
				'subscriberID' => $Subscriber->id(),
				'listID'       => $List->id(),
				'subStatus'    => $member['status'],
				'subRating'    => $member['member_rating'],
				'subCreated'   => ($member['timestamp_signup'] ? date('Y-m-d H:i:s', strtotime($member['timestamp_signup'])) : date('Y-m-d H:i:s')),
				'subUpdated'   => ($member['last_changed'] ? date('Y-m-d H:i:s', strtotime($member['last_changed'])) : null),
			]);
		}

		return $Subscription;		
	}

	public function find_subscription(PerchMailChimp_List $List, PerchMailChimp_Subscriber $Subscriber)
	{
		$sql = 'SELECT * FROM '.$this->table.' 
			WHERE listID='.$this->db->pdb((int)$List->id()).' AND subscriberID='.$this->db->pdb((int)$Subscriber->id());
		return $this->return_instance($this->db->get_row($sql));
	}

}