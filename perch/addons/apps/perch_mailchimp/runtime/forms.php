<?php

	function perch_mailchimp_form_handler($SubmittedForm)
    {
        if ($SubmittedForm->validate()) {
            $API  = new PerchAPI(1.0, 'perch_mailchimp');
            $Subscribers = new PerchMailChimp_Subscribers($API);
            $Subscribers->subscribe_from_form($SubmittedForm);
        }
        $Perch = Perch::fetch();
        PerchUtil::debug($Perch->get_form_errors($SubmittedForm->formID));
        
    }

    function perch_mailchimp_form($template, $content=array(), $return=false)
    {
        $API      = new PerchAPI(1.0, 'perch_mailchimp');
        $Template = $API->get('Template');
        $Template->set('mailchimp/'.$template, 'mailchimp');
        $html     = $Template->render($content);
        $html     = $Template->apply_runtime_post_processing($html, $content);
        
        if ($return) return $html;
        echo $html;
    }