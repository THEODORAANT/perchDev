<?php

class PerchMailChimp_Subscribers extends PerchMailChimp_Factory
{
	protected $table               = 'mailchimp_subscribers';
    protected $pk                  = 'subscriberID';
    protected $singular_classname  = 'PerchMailChimp_Subscriber';

    protected $namespace           = 'subscriber';

    protected $event_prefix        = 'mailchimp.subscriber';
	protected $master_template	   = 'mailchimp/subscribers/subscriber.html';
	
	protected $default_sort_column = 'subscriberCreated';
	protected $created_date_column = 'subscriberCreated';

	public function subscribe_from_form(PerchAPI_SubmittedForm $SubmittedForm)
	{

		$listIDs = [];

		$merge_vars = [];
        $interests  = [];
        $confirmed  = false;
        $status     = 'subscribed';

        $FormTag = $SubmittedForm->get_form_attributes();

        if ($FormTag->is_set('double_optin')) {
            if ($FormTag->double_optin()) {
            	$status = 'pending';
            }
        }

        $attr_map = $SubmittedForm->get_attribute_map('mailer');
        if (PerchUtil::count($attr_map)) {
            foreach($attr_map as $fieldID=>$merge_var) {
                switch($merge_var) {
                	case 'list':
                		if (isset($SubmittedForm->data[$fieldID])) {
                			$listIDs[] = $SubmittedForm->data[$fieldID];	
                		}
                		break;

                    case 'email':
                        $email = $SubmittedForm->data[$fieldID];
                        break;

                    case 'confirm_subscribe':
                        $confirmed = PerchUtil::bool_val($SubmittedForm->data[$fieldID]);
                        break;

                    case 'interests':
                    	$interests[$SubmittedForm->data[$fieldID]] = true;
                    	break;

                    default:
                        $merge_vars[$merge_var] = $SubmittedForm->data[$fieldID];
                        break;

                }
            }
        }

	    if (PerchUtil::count($listIDs)) {

	        $data = [];
			$data['email_address'] = $email;
			$data['status']        = $status;
			
			if (PerchUtil::count($merge_vars)) {
				$data['merge_fields']  = $merge_vars;
			}
			
			if (PerchUtil::count($interests)) {
				$data['interests']     = $interests;	
			}

			if ($confirmed) {
				$Lists = new PerchMailChimp_Lists($this->api);
				$MailChimpAPI = $this->get_api_instance();

				foreach($listIDs as $listMailChimpID) {
					$List = $Lists->get_one_by('listMailChimpID', $listMailChimpID);

					if (is_object($List)) {

						PerchUtil::debug('Subscribing to: '.$List->listTitle());

						$listID = $listMailChimpID;

						$result = $MailChimpAPI->post("lists/$listID/members", $data);

						if ($MailChimpAPI->success()) {
							
						}else{
							PerchUtil::debug($MailChimpAPI->getLastResponse());
						}

					}

				}
			}

			
		}

	}


	public function all_subscribed($Paging=false)
    {
    	$sort_val = null;
        $sort_dir = null;

        if ($Paging && $Paging->enabled()) {
            $sql = $Paging->select_sql();
            list($sort_val, $sort_dir) = $Paging->get_custom_sort_options();
        }else{
            $sql = 'SELECT';
        }

        $sql .= ' s1.* FROM '.$this->table.' s1 JOIN '.PERCH_DB_PREFIX.'mailchimp_subscriptions s2 ON s1.subscriberID=s2.subscriberID ';

        $restrictions = $this->standard_restrictions();

        if ($restrictions!='') {
            $sql .= ' WHERE 1=1 '.$restrictions;
        }

        $sql .= ' AND s2.subStatus=\'subscribed\' ';

        if ($sort_val) {
            $sql .= ' ORDER BY '.$sort_val.' '.$sort_dir;
        } else if (isset($this->default_sort_column)) {
            $sql .= ' ORDER BY ' . $this->default_sort_column . ' '.$this->default_sort_direction;
        }

        if ($Paging && $Paging->enabled()) {
            $sql .=  ' '.$Paging->limit_sql();
        }

        $results = $this->db->get_rows($sql);

        if ($Paging && $Paging->enabled()) {
            $Paging->set_total($this->db->get_count($Paging->total_count_sql()));
        }

        return $this->return_instances($results);
    }

	public function import(PerchMailChimp_List $List)
	{
		$Imports = new PerchMailChimp_Imports($this->api);

		$Import = $Imports->create([
						'importType' => 'subscribers',
						'importSourceID' => $List->id(),
						'importCount' => 100,
						'importOffset' => 0,
					]);

		if ($Import) {
			$Import->run();
		}
		
	}

	public function import_next(PerchMailChimp_Import $Import)
	{
		$Lists = new PerchMailChimp_Lists($this->api);
		$List = $Lists->find($Import->importSourceID());

		if (!is_object($List)) {
			return false;
		}

		$MailChimpAPI = $this->get_api_instance();
		$listID       = $List->listMailChimpID();

		$members = $MailChimpAPI->get("lists/$listID/members", [
				'count'  => $Import->importCount(),
				'offset' => $Import->importOffset(), 
			]);



		if ($MailChimpAPI->success()) {


			if (isset($members['members']) && PerchUtil::count($members['members'])) {
				
				$Subscriptions = new PerchMailChimp_Subscriptions($this->api);

				$all_members = $members['members'];

				foreach($all_members as $member) {
					
					$data = $this->map_fields($member);

					if (!$this->remote_subscriber_exists_locally($member['unique_email_id'])) {
						//PerchUtil::debug('Importing subscriber: '.$member['email_address']);
						$Subscriber = $this->create($data);
					}else{
						// subscriber exists
						$Subscriber = $this->get_one_by('subscriberMailChimpID', $member['unique_email_id']);
						$Subscriber->update($data);
					}

					if ($Subscriber) {
						// create subscription
						$Subscriptions->create_from_import($List, $Subscriber, $member);
					}


				}


				// Return so that the cursor within the import is moved forward
				return [
					'result' => 'success',
					'count' => PerchUtil::count($members['members']),
					'message' => sprintf('Imported %d subscribers to list %s.', PerchUtil::count($members['members']), $List->listTitle())
				];
			}else{
				return [
					'result' => 'success',
					'count' => 0,
					'message' => sprintf('Imported all subscribers to list %s.', $List->listTitle()),
				];
			}
		}else{
			PerchUtil::debug($MailChimpAPI->getLastResponse(), 'error');	
		}

		return false;
	}


	public function lookup_and_create(array $data)
	{
		if ($this->remote_subscriber_exists_locally($data['id'])) {
			return $this->get_one_by('subscriberMailChimpID', $data['id']);
		}

		$Lists = new PerchMailChimp_Lists($this->api);
		$List  = $Lists->get_one_by('listMailChimpID', $data['list_id']);

		if (!is_object($List)) {
			return false;
		}

		$MailChimpAPI = $this->get_api_instance();
		$listID       = $List->listMailChimpID();
		$hash 		  = $MailChimpAPI->subscriberHash($data['email']);

		$member = $MailChimpAPI->get("lists/$listID/members/$hash");

		if ($MailChimpAPI->success()) {

			$Subscriptions = new PerchMailChimp_Subscriptions($this->api);

			$sub_data = $this->map_fields($member);
			$sub_data['subscriberCreated'] = date('Y-m-d H:i:s');
			
			$Subscriber = $this->create($sub_data);
			
			if ($Subscriber) {
				// create subscription
				$Subscriptions->create_from_import($List, $Subscriber, $member);
			}
		}

	}

	public function get_latest_for_list(PerchMailChimp_List $List)
    {
        $sql = 'SELECT * 
				FROM '.$this->table.' s 
				JOIN '.PERCH_DB_PREFIX.'mailchimp_subscriptions subs ON s.subscriberID=subs.subscriberID 
				WHERE
					subs.subStatus=\'subscribed\'
					AND subs.listID='.$this->db->pdb((int)$List->id()).'
					AND subs.subCreated IS NOT NULL
				ORDER BY subs.subCreated DESC
				LIMIT 3';
		return $this->return_instances($this->db->get_rows($sql));
    }

	private function remote_subscriber_exists_locally($subscriberMailChimpID)
	{
		$sql = 'SELECT COUNT(*) FROM '.$this->table.' WHERE subscriberMailChimpID='.$this->db->pdb($subscriberMailChimpID);
		if ($this->db->get_count($sql) > 0) {
			return true;
		}

		return false;
	}

	private function map_fields($member)
	{
		return [
				'subscriberMailChimpID' => $member['unique_email_id'],
				'subscriberEmail'       => $member['email_address'],
				'subscriberEmailMD5'    => $member['id'],
				'subscriberFirstName'   => $member['merge_fields']['FNAME'],
				'subscriberLastName'    => $member['merge_fields']['LNAME'],
				'subscriberCreated'     => ($member['timestamp_signup'] ? date('Y-m-d H:i:s', strtotime($member['timestamp_signup'])) : null),
				'subscriberUpdated'     => ($member['last_changed'] ? date('Y-m-d H:i:s', strtotime($member['last_changed'])) : null),
			];
	}
}