<?php
    echo $HTML->title_panel([
            'heading' => $Lang->get('Listing bookings'),

        ], $CurrentUser);


    $Smartbar = new PerchSmartbar($CurrentUser, $HTML, $Lang);


    $Smartbar->add_item([
        'active' => true,
        'title' => $Lang->get('Bookings'),
        'link'  => $API->app_nav().'/bookings/',
        'icon'  => 'core/o-connect',
    ]);

    echo $Smartbar->render();

    if (PerchUtil::count($bookings)) {


        $Listing = new PerchAdminListing($CurrentUser, $HTML, $Lang, $Paging);

        $Listing->add_col([
                'title'     => $Lang->get('Booking ID'),
                'value'     => 'bookingID',
                'sort'      => 'bookingID',
                'edit_link' => 'edit',
            ]);


        $Listing->add_col([
                'title'     => $Lang->get('event'),
                'value'     => 'eventTitle',
                'sort'      => 'eventTitle',
            ]);
  $Listing->add_col([
                        'title'     => 'date',
                        'sort'      => 'date',
                        'value'     => function($Post) {
                            return date(PERCH_DATE_SHORT, strtotime($Post->date()));
                        },
                    ]);

           $Listing->add_col([
                        'title'     => 'time',
                        'sort'      => 'time',
                        'value'     => function($Post) {
                            return date(PERCH_TIME_SHORT, strtotime($Post->time()));
                        },
                    ]);

        $Listing->add_col([
                'title'     => $Lang->get('status'),
                'value'     => 'status',
                'sort'      => 'status',
            ]);

        $Listing->add_delete_action([
                'priv'   => 'perch_events.delete',
                'inline' => true,
                'path'   => 'delete',
            ]);

        echo $Listing->render($bookings);

    } // if pages

