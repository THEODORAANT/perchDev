<?php

class PerchMailChimp_Webhooks extends PerchMailChimp_Factory
{
	protected $table               = 'mailchimp_webhooks';
    protected $pk                  = 'webhookID';
    protected $singular_classname  = 'PerchMailChimp_Webhook';

    protected $namespace           = 'webhook';

    protected $event_prefix        = 'mailchimp.webhook';
	protected $master_template	   = 'mailchimp/webhooks/webhook.html';
	
	protected $default_sort_column = 'webhookID';

	public function get_base_url()
	{
		if (!defined('PERCH_MAILCHIMP_BASE_URL')) {
			define('PERCH_MAILCHIMP_BASE_URL', PerchUtil::url_to_ssl_if_needed('http://' . $_SERVER['HTTP_HOST']));
		}

		return PERCH_MAILCHIMP_BASE_URL;
	}

	public function get_webhook_url()
	{
		$Settings = $this->api->get('Settings');
		$secret   = $Settings->get('perch_mailchimp_secret')->settingValue();

		if (!$secret) {
			$secret = substr(md5(uniqid()), 0, 7);
	      	$Settings->set('perch_mailchimp_secret', $secret);   
	    }

		$base_url = $this->get_base_url();
		return $base_url . PERCH_LOGINPATH.'/addons/apps/perch_mailchimp/api/webhooks/?secret='.$secret;
	}

	public function set_up(PerchMailChimp_List $List)
	{
		// Does a webhook exist for this list?
		$existing = $this->get_by('listID', $List->id());
		if (PerchUtil::count($existing)) return;


		$MailChimpAPI = $this->get_api_instance();
		$listID       = $List->listMailChimpID();

		$webhook = $MailChimpAPI->post("lists/$listID/webhooks", [
				'url'  => $this->get_webhook_url(),
				'events' => [
								'subscribe'   => true,
								'unsubscribe' => true,
								'profile'     => true,
								'cleaned'     => true,
								'upemail'     => true,
								'campaign'    => true,
							],
				'sources' => [
								'user'  => true,
								'admin' => true,
								'api'   => false,
							]
			]);

		if ($MailChimpAPI->success()) {

			$data = $this->map_fields($webhook);
			$data['listID'] = $List->id();

			if (!$this->remote_webhook_exists_locally($webhook['id'])) {
				$Webhook = $this->create($data);
			}else{
				// Webhook exists
				$Webhook = $this->update($data);
			}

		}else{
			PerchUtil::debug($MailChimpAPI->getLastResponse(), 'error');
		}
	}

	public function import(PerchMailChimp_List $List)
	{
		$Imports = new PerchMailChimp_Imports($this->api);

		$Import = $Imports->create([
						'importType'     => 'webhooks',
						'importSourceID' => $List->id(),
						'importCount'    => 100,
						'importOffset'   => 0,
					]);

		if ($Import) {
			$Import->run();
		}
	}

	public function import_next(PerchMailChimp_Import $Import)
	{
		$Lists = new PerchMailChimp_Lists($this->api);
		$List  = $Lists->find($Import->importSourceID());

		if (!is_object($List)) {
			return false;
		}

		$MailChimpAPI = $this->get_api_instance();
		$listID       = $List->listMailChimpID();

		$webhooks = $MailChimpAPI->get("lists/$listID/webhooks", [
				'count'  => $Import->importCount(),
				'offset' => $Import->importOffset(), 
			]);

		if ($MailChimpAPI->success()) {

			if (isset($webhooks['webhooks']) && PerchUtil::count($webhooks['webhooks'])) {

				$all_webhooks = $webhooks['webhooks'];

				foreach($all_webhooks as $webhook) {
					
					$data = $this->map_fields($webhook);
					$data['listID'] = $List->id();

					if (!$this->remote_webhook_exists_locally($webhook['id'])) {
						$Webhook = $this->create($data);
					}else{
						// Webhook exists
						$Webhook = $this->get_one_by('webhookMailChimpID', $webhook['id']);
						if ($Webhook) {
							$Webhook->update($data);
						}
					}

				}


				// Return so that the cursor within the import is moved forward
				return [
					'result' => 'success',
					'count' => PerchUtil::count($webhooks['webhooks']),
					'message' => sprintf('Imported %d webhooks to list %s.', PerchUtil::count($webhooks['webhooks']), $List->listTitle())
				];
			}else{
				return [
					'result' => 'success',
					'count' => 0,
					'message' => sprintf('Imported all webhooks to list %s.', $List->listTitle()),
				];
			}
		}else{
			PerchUtil::debug($MailChimpAPI->getLastResponse(), 'error');	
		}

		return false;
	}

	private function remote_webhook_exists_locally($webhookMailChimpID)
	{
		$sql = 'SELECT COUNT(*) FROM '.$this->table.' WHERE webhookMailChimpID='.$this->db->pdb($webhookMailChimpID);
		if ($this->db->get_count($sql) > 0) {
			return true;
		}

		return false;
	}

	private function map_fields($member)
	{
		return [
				'webhookMailChimpID' => $member['id'],
				'webhookURL'         => $member['url'],
			];
	}


}