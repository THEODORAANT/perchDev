<?php

	PerchUI::set_subnav([
		[
			'page'=>[

					'perch_events',
					'perch_events/delete',
					'perch_events/edit'
			],
			 'label'=>'Add/Edit'
		],
		[
			'page'=>[

					'perch_events/categories',
					'perch_events/categories/edit',
					'perch_events/categories/delete',

			],
			 'label'=>'Categories', 'priv'=>'perch_events.categories.manage'
		],[
                  			'page'=>[

                  					'perch_events/bookings',
                  					'perch_events/bookings/edit',
                  					'perch_events/bookings/delete',

                  			],
                  			 'label'=>'Bookings', 'priv'=>'perch_events.bookings.manage'
                  		],

				[
        			'page'=>[

        					'perch_events/timeslots',
        					'perch_events/timeslots/edit',
        					'perch_events/timeslots/delete',

        			],
        			 'label'=>'Time Slots', 'priv'=>'perch_events.timeslots.manage'
        		],
	], $CurrentUser);
