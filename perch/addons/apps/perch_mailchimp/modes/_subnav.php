<?php
	PerchUI::set_subnav([
		[
			'page' => [
						'perch_mailchimp',
						'perch_mailchimp/lists/edit',
					],
			'label' => 'Lists'
		],
		[
			'page'=>[
					'perch_mailchimp/subscribers',
					],
			'label'=>'Subscribers'
		],
		[
			'page'=>[
					'perch_mailchimp/campaigns',
					],
			'label'=>'Campaigns'
		],
		[
			'page'=>[
					'perch_mailchimp/webhooks',
					],
			'label'=>'Webhooks'
		],

	], $CurrentUser);