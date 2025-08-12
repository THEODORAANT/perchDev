<?php

    function perch_mailchimp_campaigns($opts=array(), $return=false)
    {
        $defaults = [
            'list'       => null,        
            'template'   => 'campaigns/list',
            'sort'       => 'campaignSendTime',
            'sort-order' => 'DESC',
            'paginate'   => false,
        ];

        if (is_array($opts)) {
            $opts = array_merge($defaults, $opts);
        }else{
            $opts = $defaults;
        }

        if (isset($opts['data'])) PerchSystem::set_vars($opts['data']);

        $API  = new PerchAPI(1.0, 'perch_mailchimp');
        $Campaigns = new PerchMailChimp_Campaigns($API);

        $r = $Campaigns->get_custom($opts);
        
        if ($return) return $r;
        
        echo $r;
    }

    function perch_mailchimp_campaign_content($slug, $opts=array(), $return=false)
    {
		$API       = new PerchAPI(1.0, 'perch_mailchimp');
		$Campaigns = new PerchMailChimp_Campaigns($API);
		$Campaign  = $Campaigns->get_one_by('campaignSlug', $slug);

        if (isset($opts['data'])) PerchSystem::set_vars($opts['data']);

        if ($Campaign && $Campaign->is_public()) {
            if ($return) return $Campaign->get_html();
            echo $Campaign->get_html();
        }

        return false;
    }