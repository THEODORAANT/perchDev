<?php
	$Item     = false;
	$details   = false;

	if (PerchUtil::get('id')) {

		if (!$CurrentUser->has_priv($delete_priv)) {
		    PerchUtil::redirect($API->app_path());
		}

		$Item = $Factory->find(PerchUtil::get('id'));
	}else{
		PerchUtil::redirect($API->app_path('perch_mailchimp').$return_path);
	}


	$Form = $API->get('Form');
	$Form->set_name('delete');
	
	if ($Form->submitted()) {
	
		if ($Item) {
			$Item->delete();	
		}

		if ($Form->submitted_via_ajax) {
		    echo $API->app_path('perch_mailchimp').$return_path;
		    exit;
		}else{
		   PerchUtil::redirect($API->app_path('perch_mailchimp').$return_path); 
		}
			
			
	}

	if (!$Item) {
		PerchUtil::redirect($API->app_path('perch_mailchimp').$return_path);
	}

