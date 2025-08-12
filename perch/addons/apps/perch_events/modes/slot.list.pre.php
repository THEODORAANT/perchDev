<?php

	if (!$CurrentUser->has_priv('perch_events.timeslots.manage')) {
        exit;
    }

    $TimeSlots = new PerchEvents_TimeSlots($API);

    $timeSlots = $TimeSlots->all();

