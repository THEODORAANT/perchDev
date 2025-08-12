<?php

class PerchMailChimp_Campaigns extends PerchMailChimp_Factory
{
	protected $table               = 'mailchimp_campaigns';
    protected $pk                  = 'campaignID';
    protected $singular_classname  = 'PerchMailChimp_Campaign';

    protected $namespace           = 'mailchimp';

    protected $event_prefix        = 'mailchimp.campaign';
	protected $master_template	   = 'mailchimp/campaigns/campaign.html';
	
	protected $default_sort_column = 'campaignCreated';
	protected $default_sort_direction = 'DESC';
	protected $created_date_column = 'campaignCreated';


	public function get_custom($opts)
	{
		$opts['template'] = 'mailchimp/'.$opts['template'];
		
		return $this->get_filtered_listing($opts, function(PerchQuery $Query) use ($opts) {

			$DB = $this->api->get('DB');

			$Query->from .= ', '.PERCH_DB_PREFIX.'mailchimp_lists l ';
			$Query->where[] = 'main.listID=l.listID';
			$Query->where[] = 'l.listPublic=1';

			if ($opts['list']) {
				$Query->where[] = 'l.listMailChimpID='.$DB->pdb($opts['list']);
			}

			return $Query;
		});
	}

	public function import()
	{
		$Imports = new PerchMailChimp_Imports($this->api);

		$Import = $Imports->create([
						'importType'     => 'campaigns',
						'importSourceID' => null,
						'importCount'    => 20,
						'importOffset'   => 0,
					]);

		if ($Import) {
			$Import->run();
		}
		
	}

	public function import_next(PerchMailChimp_Import $Import)
	{
		$MailChimpAPI = $this->get_api_instance();

		$campaigns = $MailChimpAPI->get("campaigns", [
				'count'  => $Import->importCount(),
				'offset' => $Import->importOffset(), 
			]);

		if ($MailChimpAPI->success()) {

			if (isset($campaigns['campaigns']) && PerchUtil::count($campaigns['campaigns'])) {
				
				$Subscriptions = new PerchMailChimp_Subscriptions($this->api);
				$Lists = new PerchMailChimp_Lists($this->api);

				$all_campaigns = $campaigns['campaigns'];

				foreach($all_campaigns as $campaign) {
					
					$data = $this->map_fields($campaign);

					$List = $Lists->get_one_by('listMailChimpID', $campaign['recipients']['list_id']);
					if ($List) {
						$data['listID'] = $List->id();	
					}
					

					if (!$this->remote_campaign_exists_locally($campaign['id'])) {
						$Campaign = $this->create($data);
					}else{
						// Campaign exists
						$Campaign = $this->get_one_by('campaignMailChimpID', $campaign['id']);
						$Campaign->update($data);
					}

					if ($Campaign) {
						$Campaign->import_content($MailChimpAPI);
					}

				}

				// Return so that the cursor within the import is moved forward
				return [
					'result' => 'success',
					'count' => PerchUtil::count($campaigns['campaigns']),
					'message' => sprintf('Imported %d campains', PerchUtil::count($campaigns['campaigns']))
				];
			}else{
				return [
					'result' => 'success',
					'count' => 0,
					'message' => sprintf('Imported all campaigns.'),
				];
			}
		}else{
			PerchUtil::debug($MailChimpAPI->getLastResponse(), 'error');	
		}

		return false;
	}

	public function import_one($campaignMailChimpID)
	{
		$MailChimpAPI = $this->get_api_instance();

		$campaign = $MailChimpAPI->get("campaigns/$campaignMailChimpID");

		if ($MailChimpAPI->success()) {

			$data = $this->map_fields($campaign);

			$Lists = new PerchMailChimp_Lists($this->api);
			$List = $Lists->get_one_by('listMailChimpID', $campaign['recipients']['list_id']);
			if ($List) {
				$data['listID'] = $List->id();	
			}

			if (!$this->remote_campaign_exists_locally($campaign['id'])) {
				$Campaign = $this->create($data);
			}else{
				// Campaign exists
				$Campaign = $this->get_one_by('campaignMailChimpID', $campaign['id']);
				$Compaign->update($data);
			}

			if ($Campaign) {
				$Campaign->import_content($MailChimpAPI);
			}
			
		}else{
			PerchUtil::debug($MailChimpAPI->getLastResponse(), 'error');	
		}

		return false;
	}

	private function remote_campaign_exists_locally($campaignMailChimpID)
	{
		$sql = 'SELECT COUNT(*) FROM '.$this->table.' WHERE campaignMailChimpID='.$this->db->pdb($campaignMailChimpID);
		if ($this->db->get_count($sql) > 0) {
			return true;
		}

		return false;
	}

	private function map_fields($campaign)
	{
		return [
				'campaignMailChimpID' => $campaign['id'],
				'campaignSendTime'    => ($campaign['send_time'] ? date('Y-m-d H:i:s', strtotime($campaign['send_time'])) : null),
				'campaignArchiveURL'  => $campaign['archive_url'],
				'campaignStatus'      => $campaign['status'],
				'campaignEmailsSent'  => $campaign['emails_sent'],
				'campaignSubject'     => $campaign['settings']['subject_line'],
				'campaignTitle'       => ($campaign['settings']['title'] ? $campaign['settings']['title'] : $campaign['settings']['subject_line']),
				'campaignSlug'        => PerchUtil::urlify($campaign['settings']['subject_line']),
				'campaignCreated'     => ($campaign['create_time'] ? date('Y-m-d H:i:s', strtotime($campaign['create_time'])) : null),
			];
	}
}