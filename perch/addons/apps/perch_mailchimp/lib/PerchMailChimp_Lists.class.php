<?php

class PerchMailChimp_Lists extends PerchMailChimp_Factory
{
	protected $table               = 'mailchimp_lists';
    protected $pk                  = 'listID';
    protected $singular_classname  = 'PerchMailChimp_List';

    public $static_fields          = ['listMailChimpID', 'listTitle', 'listMemberCount', 'listMemberCountSinceLastSend', 'listUnsubsSinceLastSend', 'listOpenRate', 'listClickRate', 'listLastSend', 'listPublic', 'listSearchable'];

    protected $namespace           = 'list';

    protected $event_prefix        = 'mailchimp.list';
	protected $master_template	   = 'mailchimp/lists/list.html';
	
	protected $default_sort_column = 'listTitle';
	protected $created_date_column = 'listCreated';

	public function import()
	{
		$MailChimpAPI = $this->get_api_instance();
		
		$lists = $MailChimpAPI->get("lists");

		if ($MailChimpAPI->success()) {
			if (isset($lists['lists']) && PerchUtil::count($lists['lists'])) {
				$all_lists = $lists['lists'];

				foreach($all_lists as $list) {

					$data = $this->map_fields($list);

					if (!$this->remote_list_exists_locally($list['id'])) {
						PerchUtil::debug('Importing list: '.$list['id']);
						$this->create($data);
					}else{
						$Lists = new PerchMailChimp_Lists($this->api);
						$List = $Lists->get_one_by('listMailChimpID', $list['id']);
						if ($List) {
							$List->update($data);
						}
					}
				}
			}
		}else{
			PerchUtil::debug($MailChimpAPI->getLastResponse(), 'error');	
		}
	}

	private function remote_list_exists_locally($mailchimpListID)
	{
		$sql = 'SELECT COUNT(*) FROM '.$this->table.' WHERE listMailChimpID='.$this->db->pdb($mailchimpListID);
		if ($this->db->get_count($sql) > 0) {
			return true;
		}

		return false;
	}

	private function map_fields($list)
	{
		return [
				'listMailChimpID'              => $list['id'],
				'listTitle'                    => $list['name'],
				'listMemberCount'              => $list['stats']['member_count'],
				'listMemberCountSinceLastSend' => $list['stats']['member_count_since_send'],
				'listUnsubsSinceLastSend'      => $list['stats']['unsubscribe_count_since_send'],
				'listOpenRate'                 => $list['stats']['open_rate'],
				'listClickRate'                => $list['stats']['click_rate'],
				'listLastSend'                 => ($list['stats']['campaign_last_sent'] ? date('Y-m-d H:i:s', strtotime($list['stats']['campaign_last_sent'])) : null),
				'listCreated'                  => date('Y-m-d H:i:s', strtotime($list['date_created'])),
			];
	}

}