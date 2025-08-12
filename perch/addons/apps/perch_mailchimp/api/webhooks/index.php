<?php
	use \DrewM\MailChimp\Webhook as MailChimpWebhook;

	include(__DIR__.'/../../env_runtime.php');

	$API = new PerchAPI(1.0, 'perch_mailchimp');
	$Settings = $API->get('Settings');
	$secret   = $Settings->get('perch_mailchimp_secret')->val();

	if ($secret != perch_get('secret')) {
		die('Unauthorised.');
	}

	$Subscribers = new PerchMailChimp_Subscribers($API);

	MailChimpWebhook::subscribe('subscribe', function($data) use ($API, $Subscribers) {	
		$Subscriber = $Subscribers->get_one_by('subscriberMailChimpID', $data['id']);

		if (!$Subscriber) {
			$Subscriber = $Subscribers->lookup_and_create($data);
		}

		if ($Subscriber) {
			$Subscriber->update_subscription($data['list_id'], 'subscribe');
		}

		$Lists = new PerchMailChimp_Lists($API);
		$Lists->import();

	});

	MailChimpWebhook::subscribe('unsubscribe', function($data) use ($API, $Subscribers) {		
		$Subscriber = $Subscribers->get_one_by('subscriberMailChimpID', $data['id']);
		if ($Subscriber) {
			$Subscriber->update_subscription($data['list_id'], 'unsubscribed');
		}

		$Lists = new PerchMailChimp_Lists($API);
		$Lists->import();
	});

	MailChimpWebhook::subscribe('cleaned', function($data) use ($API, $Subscribers) {		
		$Subscriber = $Subscribers->get_one_by('subscriberMailChimpID', $data['id']);
		if ($Subscriber) {
			$Subscriber->update_subscription($data['list_id'], 'cleaned');
		}

		$Lists = new PerchMailChimp_Lists($API);
		$Lists->import();
	});

	MailChimpWebhook::subscribe('upemail', function($data) use ($API, $Subscribers) {
		$Subscriber = $Subscribers->get_one_by('subscriberEmail', $data['old_email']);
		if ($Subscriber) {
			
			$Subscriber->update([
					'subscriberEmail' => $data['new_email'],
					'subscriberMailChimpID' => $data['new_id'],
				]);
		}
	});

	MailChimpWebhook::subscribe('campaign', function($data) use ($API) {
		$Campaigns = new PerchMailChimp_Campaigns($API);
		$Campaigns->import_one($data['id']);

		$Lists = new PerchMailChimp_Lists($API);
		$Lists->import();
	});

	# Uncomment for debug
	#file_put_contents(time().'_log.txt', print_r(MailChimpWebhook::receive(), 1));	

