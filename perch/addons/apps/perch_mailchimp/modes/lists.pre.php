<?php
	$Form = $API->get('Form');

	$Paging = $API->get('Paging');
	$Paging->set_per_page(20);

	$Lists = new PerchMailChimp_Lists($API);

	$lists = $Lists->all($Paging);

	if (!PerchUtil::count($lists)) {
		// No lists! gasp!

		// Do we have a license key?
		$Settings  = PerchSettings::fetch();
		$api_key   = $Settings->get('perch_mailchimp_api_key')->val();

		if ($api_key) {
			$Lists->attempt_install();

			$Lists->import();

			$lists = $Lists->all();
		}else{

			$message = $HTML->warning_message('Please add your MailChimp API key on the Settings page.');

		}

		
	}else{
		if ($Form->submitted()) {
			$Lists->import();

			$lists = $Lists->all();

			$message = $HTML->success_message('Lists updated.');
		}
	}
