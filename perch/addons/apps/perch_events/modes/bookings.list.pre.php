<?php

	if (!$CurrentUser->has_priv('perch_events.bookings.manage')) {
        exit;
    }

    $Bookings = new PerchEvents_Bookings($API);
    $bookings = $Bookings->all();
