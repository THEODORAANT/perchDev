<?php
	include(__DIR__.'/fieldtypes.php');
	include(__DIR__.'/lib/vendor/autoload.php');

	spl_autoload_register(function($class_name){
		if (strpos($class_name, 'PerchMailChimp_')===0) {
			include(PERCH_PATH.'/addons/apps/perch_mailchimp/lib/'.$class_name.'.class.php');
			return true;
		}
		return false;
	});

	#PerchSystem::register_template_handler('PerchMailChimp_Template');
	PerchSystem::register_search_handler('PerchMailChimp_SearchHandler');

	include(__DIR__.'/runtime/forms.php');
	include(__DIR__.'/runtime/campaigns.php');
	include(__DIR__.'/events.php');