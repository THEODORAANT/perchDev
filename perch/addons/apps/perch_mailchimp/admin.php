<?php

    if ($CurrentUser->logged_in() && $CurrentUser->has_priv('perch_mailchimp')) {
        $this->register_app('perch_mailchimp', 'MailChimp', 10, 'Integrate with the MailChimp email list service', '3.1');
        $this->require_version('perch_mailchimp', '3.0');

        $this->add_setting('perch_mailchimp_api_key', 'MailChimp API Key', 'text', '');
        $this->add_setting('perch_mailchimp_campaign_url', 'Campaign archive page', 'text', '/mailchimp/campaign/{campaignSlug}');
    }

    include(__DIR__.'/lib/vendor/autoload.php');

    spl_autoload_register(function($class_name){
        if (strpos($class_name, 'PerchMailChimp_')===0) {
            include(PERCH_PATH.'/addons/apps/perch_mailchimp/lib/'.$class_name.'.class.php');
            return true;
        }
        return false;
    });

    // Fieldtypes
    include_once(__DIR__.'/fieldtypes.php');

    // event listeners
    include_once(__DIR__.'/events.php');