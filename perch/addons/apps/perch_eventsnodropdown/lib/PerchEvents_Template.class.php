<?php

class PerchEvents_Template extends PerchAPI_TemplateHandler
{
	public $tag_mask = 'events|timeslots|bookings';

	public function render( $contents, $Template)
	{

        if (strpos($contents, 'perch:timeslots')!==false) {
			$contents       = $this->parse_paired_tags('timeslots', true, $contents, $vars, 0, 'render_timeslots');
        }

		return $contents;
	}



}
