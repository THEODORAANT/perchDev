<?php

class PerchMailChimp_Webhook extends PerchAPI_Base
{
	protected $factory_classname = 'PerchMailChimp_Webhooks';
	protected $table             = 'mailchimp_webhooks';
	protected $pk                = 'webhookID';
}