<?php
	
	#PerchScheduledTasks::register_task('perch_events', 'update_category_counts', 60, 'scheduled_events_update_category_counts');

	function scheduled_events_update_category_counts($last_run)
	{
		$API  = new PerchAPI(1.0, 'perch_events'); 
		$Categories = new PerchEvents_Categories();
		$Categories->update_event_counts();

		return array(
				'result'=>'OK',
				'message'=>'Event category counts updated.'
			);
	}
?>