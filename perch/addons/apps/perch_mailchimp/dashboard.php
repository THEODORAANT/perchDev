<?php

	return function($CurrentUser) {

		$API   = new PerchAPI(1.0, 'perch_mailchimp');
    	$Lang  = $API->get('Lang');
    	$HTML  = $API->get('HTML');


    	// check privs
	    if($CurrentUser->has_priv('perch_mailchimp')) {

		    $Settings = $API->get('Settings');
		    $api_key  = $Settings->get('perch_mailchimp_api_key')->val();
		    
		    $Lists = new PerchMailChimp_Lists($API);
		    $Subscribers = new PerchMailChimp_Subscribers($API);
		    $lists = [];
		    
		    $msg = false;
		    if(!$api_key || $api_key == '') {
		    	return;
		    }else{    	
		    	$lists = $Lists->all();
		    }

		    $header  = $HTML->wrap('header h2', $Lang->get('MailChimp'));

		    $body = '';

		    if (PerchUtil::count($lists)) {

		    	$items = '';

		    	foreach($lists as $List) {

		    		$s = '';

		    		$s .= $HTML->wrap('h3', $HTML->encode($List->listTitle()));

		    		$subs = $Subscribers->get_latest_for_list($List);

		    		if(is_array($subs) && PerchUtil::count($subs)>0) {
						
						$sublis = '';
							
						foreach($subs as $Subscriber) {
							$sublis .= $HTML->wrap('li', $HTML->encode($Subscriber->subscriberEmail()).' <span class="note">'.strftime('%d %b %Y', strtotime($Subscriber->subCreated()))).'</span>'."\n";
						}
						
						$s .= $HTML->wrap('ul.dash-list', $sublis);
					}


		    		$items[] = $s;
		    	}

		    	$body .= implode('', $items);
		    }

		    return $HTML->wrap('div.widget div.dash-content', $header.$body);
		}
	};
