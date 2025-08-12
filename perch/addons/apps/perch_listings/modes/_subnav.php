<?php
	PerchUI::set_subnav([
		[

			'page'=>[
						'perch_listings',
						'perch_listings/listings',
						'perch_listings/listings/edit',
						'perch_listings/listings/delete',


			], 
			'label'=>'Listings'
		],
	[
			'page'=>[

					'perch_listings/categories',
					'perch_listings/categories/edit',
					'perch_listings/categories/delete',

			],
			 'label'=>'Categories', 'priv'=>'perch_listings.categories.manage'
		],


	], $CurrentUser);
