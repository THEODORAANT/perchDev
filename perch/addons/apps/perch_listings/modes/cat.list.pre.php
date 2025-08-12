<?php
    
	if (!$CurrentUser->has_priv('perch_listings.categories.manage')) {
        exit;
    }
    
    $Categories = new PerchListings_Categories($API);

    $categories = $Categories->all();
    
