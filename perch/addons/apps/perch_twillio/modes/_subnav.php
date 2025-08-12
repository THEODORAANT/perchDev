<?php
	PerchUI::set_subnav([
		[
			'page'=>[
						'perch_twillio',
						'perch_twillio/message',
						'perch_twillio/message/edit',
						'perch_twillio/message/delete',


			], 
			'label'=>'Messages'
		],
[
			'page'=>[

						'perch_twillio/dispatches',
						'perch_twillio/dispatches/edit',
						'perch_twillio/dispatches/delete',


			],
			'label'=>'Dispatches'
		],


	], $CurrentUser);
