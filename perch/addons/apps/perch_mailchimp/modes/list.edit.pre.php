<?php
	$Lists = new PerchMailChimp_Lists($API);
	
	$edit_mode = false;
	$List     = false;
	$list_id = false;
	$message   = false;
	$details   = false;

	if (PerchUtil::get('id')) {

		if (!$CurrentUser->has_priv('perch_mailchimp.lists.edit')) {
		    PerchUtil::redirect($API->app_path());
		}

		$list_id = PerchUtil::get('id');
		$List     = $Lists->find($list_id);
		$edit_mode = true;

	}else{
		if (!$CurrentUser->has_priv('perch_mailchimp.lists.create')) {
		    PerchUtil::redirect($API->app_path());
		}
	}

	// Template
	$Template   = $API->get('Template');
	$Template->set('mailchimp/lists/list.html', 'mailchimp');
	$tags = $Template->find_all_tags_and_repeaters();

	$Form = $API->get('Form');
	$Form->handle_empty_block_generation($Template);

	$Form->set_required_fields_from_template($Template, $details);

	if ($Form->submitted()) {

		$data = $Form->get_posted_content($Template, $Lists, $List);
		
		if ($List) {
			$List->update($data);	
		}else{
			$List = $Lists->create($data);

			if ($List) {
				PerchUtil::redirect($Perch->get_page().'?id='.$List->id().'&created=1');	
			}
			
		}

		if (is_object($List)) {
		    $message = $HTML->success_message('Your list has been successfully edited. Return to %slisting%s', '<a class="notification-link" href="'.$API->app_path('perch_mailchimp') .'/">', '</a>');
		}else{
		    $message = $HTML->failure_message('Sorry, that update was not successful.');
		}

	}

	if (PerchUtil::get('created') && !$message) {
	    $message = $HTML->success_message('Your list has been successfully created. Return to %s listing%s', '<a class="notification-link" href="'. $API->app_path('perch_mailchimp') .'/">', '</a>');
	}


	if (is_object($List)) {
		$details = $List->to_array();
	}