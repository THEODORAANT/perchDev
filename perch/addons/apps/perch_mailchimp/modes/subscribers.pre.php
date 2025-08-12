<?php

	$Form = $API->get('Form');

	$Paging = $API->get('Paging');
	$Paging->set_per_page(20);

	$Lists = new PerchMailChimp_Lists($API);
	$lists = $Lists->all();

	$Subscribers = new PerchMailChimp_Subscribers($API);

	// Run an import if there is one
	$Imports = new PerchMailChimp_Imports($API);
	$Import = $Imports->get_next_import('subscribers');
	
	if (is_object($Import)) {

		$message = $HTML->warning_message('Subscribers are still updating.');

		$Import->run();
	}else{

		if ($Form->submitted()) {
			foreach($lists as $List) {
				$Subscribers->import($List);
			}
		}

	}


	$subscribers = $Subscribers->all_subscribed($Paging);

	if (!PerchUtil::count($subscribers)) {
		foreach($lists as $List) {
			$Subscribers->import($List);
		}

		$subscribers = $Subscribers->all_subscribed($Paging);
	}
