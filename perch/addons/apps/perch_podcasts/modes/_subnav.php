<?php
	PerchUI::set_subnav([
		[
			'page'=>[

					'perch_podcasts',
					'perch_podcasts/delete',
					'perch_podcasts/edit',
					'perch_podcasts/show',
					'perch_podcasts/show/import',
					'perch_podcasts/show/episode',
			], 
			'label'=>'Shows'
		],
		[
			'page'=>[
					'perch_podcasts/stats',
				], 
			'label'=>'Stats'
		],
		
	], $CurrentUser);