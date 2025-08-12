<?php

class PerchMailChimp_Subscription extends PerchAPI_Base
{
	protected $factory_classname = 'PerchMailChimp_Subscriptions';
	protected $table             = 'mailchimp_subscriptions';
	protected $pk                = 'subID';

	protected $modified_date_column = 'subUpdated';
}