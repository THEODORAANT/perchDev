<?php

use \DrewM\MailChimp\MailChimp as MailChimpAPI;

class PerchMailChimp_Factory extends PerchAPI_Factory
{
	private $api_instance = null;


	/**
	 * Get an instance of the DrewM\MailChimp library
	 */
	protected function get_api_instance()
	{
		if (is_object($this->api_instance)) {
			return $this->api_instance;
		}

		$Settings  = PerchSettings::fetch();
		$api_key   = $Settings->get('perch_mailchimp_api_key')->val();

		$instance = new MailChimpAPI($api_key);

		if (is_object($instance)) {
			$instance->verify_ssl = false;
			$this->api_instance = $instance;
			return $instance;
		}

		return false;
	}

	public function get_custom($opts)
	{
		$opts['template'] = 'mailchimp/'.$opts['template'];
		
		return $this->get_filtered_listing($opts);
	}

}