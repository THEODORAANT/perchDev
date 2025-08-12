<?php

	$Form = $API->get('Form');

	$Paging = $API->get('Paging');
	$Paging->set_per_page(20);

	$Campaigns = new PerchMailChimp_Campaigns($API);
	

	// Run an import if there is one
	$Imports = new PerchMailChimp_Imports($API);
	$Import = $Imports->get_next_import('campaigns');
	
	if (is_object($Import)) {
		$message = $HTML->warning_message('Campaigns are still updating.');
		$Import->run();
	}else{

		if ($Form->submitted()) {
			$Campaigns->import();
			$campaigns = $Campaigns->all($Paging);
		}


	}

	$campaigns = $Campaigns->all($Paging);

	if (!PerchUtil::count($campaigns)) {
		$Campaigns->import();
		$campaigns = $Campaigns->all($Paging);
	}

	
