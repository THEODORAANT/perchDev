<?php
	$Form = $API->get('Form');

	$Paging = $API->get('Paging');
	$Paging->set_per_page(20);

	$Lists = new PerchMailChimp_Lists($API);
	$lists = $Lists->all();

	// Run an import if there is one
	$Imports = new PerchMailChimp_Imports($API);
	$Import = $Imports->get_next_import('webhooks');

	$Webhooks = new PerchMailChimp_Webhooks($API);
	
	if (is_object($Import)) {

		$message = $HTML->warning_message('Webhooks are still importing.');

		$Import->run();
	}else{
		if ($Form->submitted()) {
			foreach($lists as $List) {
				$Webhooks->import($List);
				$Webhooks->set_up($List);
			}
		}
	}

	$webhooks = $Webhooks->all($Paging);

	if (!PerchUtil::count($webhooks)) {
		foreach($lists as $List) {
			$Webhooks->import($List);
			$Webhooks->set_up($List);
		}

		$webhooks = $Webhooks->all($Paging);
	}
