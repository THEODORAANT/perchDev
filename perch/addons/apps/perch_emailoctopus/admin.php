<?php

    if ($CurrentUser->logged_in() && $CurrentUser->has_priv('perch_emailoctopus')) {
        $this->register_app('perch_emailoctopus', 'Email Octopus ', 10, 'Integrate with the Email Octopus email list service', '1.0');


        $this->add_setting('perch_emailoctopus_api_key', 'Email Octopus API Key', 'text', '');
      //  $this->add_setting('perch_emailoctopus_campaign_url', 'Campaign archive page', 'text', '/perch_emailoctopus/campaign/{campaignSlug}');
    }

    include(__DIR__.'/lib/vendor/autoload.php');

    spl_autoload_register(function($class_name){
        if (strpos($class_name, 'PerchEmailOctopus_')===0) {
            include(PERCH_PATH.'/addons/apps/perch_emailoctopus/lib/'.$class_name.'.class.php');
            return true;
        }
        return false;
    });

    // Fieldtypes
    include_once(__DIR__.'/fieldtypes.php');

    // event listeners
    include_once(__DIR__.'/events.php');
