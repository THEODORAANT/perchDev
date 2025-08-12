<?php

class PerchMailChimp_Campaign extends PerchAPI_Base
{
	protected $factory_classname = 'PerchMailChimp_Campaigns';
	protected $table             = 'mailchimp_campaigns';
	protected $pk                = 'campaignID';

	protected $modified_date_column = 'campaignUpdated';

	public function import_content(\DrewM\MailChimp\MailChimp $MailChimpAPI)
	{
		// import content
		$campaignID = $this->campaignMailChimpID();
		$content = $MailChimpAPI->get("campaigns/$campaignID/content");

		if ($MailChimpAPI->success()) {
			
			$this->update([
				'campaignText' => $content['plain_text'],
				'campaignHTML' => $content['html'],
			]);

		}
	}

	public function to_array($template_ids=false)
    {	
    	$out = parent::to_array();

    	if (PerchUtil::count($template_ids) && in_array('campaignURL', $template_ids)) {
            $out['campaignURL'] = $this->campaignURL();
        }

        
        $Lists = new PerchMailChimp_Lists($this->api);
        $List = $Lists->find((int)$this->listID());
        if ($List) {
        	$out = array_merge($out, $List->to_array());
        }
    

        return $out;
    }

    public function campaignURL()
    {
		$Settings           = PerchSettings::fetch();
		$url_template       = $Settings->get('perch_mailchimp_campaign_url')->val();
		$url_vars = $this->details;
		$out                = preg_replace_callback('/{([A-Za-z0-9_\-]+)}/', function($matches) use ($url_vars) {
									if (isset($url_vars[$matches[1]])){
							            return $url_vars[$matches[1]];
							        }
								}, $url_template);

        return $out;
    }

    public function get_html()
    {
    	$html = $this->campaignHTML();
    	$html = str_replace([
    			'*|MC:SUBJECT|*',
    			'*|ARCHIVE|*',
    			'*|CAMPAIGN_UID|*',
    		], [
    			$this->campaignSubject(),
    			$this->campaignArchiveURL(),
    			$this->campaignMailChimpID(),
    		], $html);
    	return $html;
    }

    public function is_public()
    {
        $Lists = new PerchMailChimp_Lists($this->api);
        $List = $Lists->find((int)$this->listID());
        if ($List) {
            if ($List->listPublic()) {
                return true;
            }
        }
        return false;
    }
}