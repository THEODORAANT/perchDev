<?php
	PerchUI::set_subnav([
		[
			'page' => [
						'perch_emailoctopus',
						'perch_emailoctopus/lists/edit',
					],
			'label' => 'Lists'
		],
		[
			'page'=>[
					'perch_emailoctopus/subscribers',
					],
			'label'=>'Subscribers'
		],
		[
			'page'=>[
					'perch_emailoctopus/campaigns',
					],
			'label'=>'Campaigns'
		],
	/*	[
			'page'=>[
					'perch_emailoctopus/webhooks',
					],
			'label'=>'Webhooks'
		],*/

	], $CurrentUser);
