<?php

	function perch_emailoctopus_form_handler($SubmittedForm)
    {
        if ($SubmittedForm->validate()) {
            $API  = new PerchAPI(1.0, 'perch_emailoctopus');
            $Subscribers = new PerchEmailOctopus_Subscribers($API);
            $Subscribers->subscribe_from_form($SubmittedForm);
        }
        $Perch = Perch::fetch();
        PerchUtil::debug($Perch->get_form_errors($SubmittedForm->formID));

    }

    function perch_emailoctopus_form($template, $content=array(), $return=false)
    {
        $API      = new PerchAPI(1.0, 'perch_emailoctopus');
        $Template = $API->get('Template');
        $Template->set('emailoctopus/'.$template, 'emailoctopus');
        $html     = $Template->render($content);
        $html     = $Template->apply_runtime_post_processing($html, $content);

        if ($return) return $html;
        echo $html;
    }
