<?php
	//include(__DIR__.'/fieldtypes.php');
//	include(__DIR__.'/lib/vendor/autoload.php');

	spl_autoload_register(function($class_name){
		if (strpos($class_name, 'PerchEmailOctopus_')===0) {
			include(PERCH_PATH.'/addons/apps/perch_emailoctopus/lib/'.$class_name.'.class.php');
			return true;
		}
		return false;
	});

	#PerchSystem::register_template_handler('Perchemailoctopus_Template');
	#PerchSystem::register_search_handler('PerchMemailoctopus_SearchHandler');

	include(__DIR__.'/runtime/forms.php');
	//include(__DIR__.'/runtime/campaigns.php');
	//include(__DIR__.'/events.php');
