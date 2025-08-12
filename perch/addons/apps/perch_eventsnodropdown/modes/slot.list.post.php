<?php
    echo $HTML->title_panel([
            'heading' => $Lang->get('Listing time slots'),
            'button'  => [
                'text' => $Lang->get('Add slot'),
                'link' => $API->app_nav().'/timeslots/edit/',
                'icon' => 'core/plus',
                'priv' => 'perch_events.timeslots.manage',
                ],
        ], $CurrentUser);


    $Smartbar = new PerchSmartbar($CurrentUser, $HTML, $Lang);


    $Smartbar->add_item([
        'active' => true,
        'title' => $Lang->get('Time Slots'),
        'link'  => $API->app_nav().'/timeslots/',
        'icon'  => 'core/o-connect',
    ]);

    echo $Smartbar->render();

    if (PerchUtil::count($timeSlots)) {


        $Listing = new PerchAdminListing($CurrentUser, $HTML, $Lang, $Paging);

        $Listing->add_col([
                'title'     => $Lang->get('TimeSlot'),
                'value'     => 'slotID',
                'sort'      => 'slotID',
                'edit_link' => 'edit',
            ]);

        $Listing->add_col([
                'title'     => $Lang->get('Slot Duration'),
                'value'     => 'slot_duration',
                'sort'      => 'slot_duration',
            ]);



      /*  $Listing->add_delete_action([
                'priv'   => 'perch_events.delete',
                'inline' => true,
                'path'   => 'delete',
            ]);*/

        echo $Listing->render($timeSlots);

    } // if pages

