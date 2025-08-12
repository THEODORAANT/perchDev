<?php

	PerchScheduledTasks::register_task('perch_mailchimp', 'import_data', 1, function($last_run){

		$API  = new PerchAPI(1.0, 'perch_mailchimp');

		$Imports = new PerchMailChimp_Imports($API);
		$result = $Imports->run_next_import();

		if ($result && $result['result'] == 'success') {
			return array(
				'result' => 'OK',
				'message' => $result['message'],
			);	
		}else{
			return array(
				'result' => 'FAILED',
				'message' => 'Unknown.',
			);
		}

		return false;

	});