<?php


class PerchAnnouncements_Factory extends PerchAPI_Factory
{
	private $api_instance = null;



 public function to_array()
    {
    	$out = parent::to_array();

    /*	if ($out['categoryDynamicFields'] != '') {
            $dynamic_fields = PerchUtil::json_safe_decode($out['categoryDynamicFields'], true);
            if (PerchUtil::count($dynamic_fields)) {
                foreach($dynamic_fields as $key=>$value) {
                    $out['perch_'.$key] = $value;
                }
            }
            $out = array_merge($dynamic_fields, $out);
        }*/

        return $out;
    }
	/*public function get_custom($opts)
	{
		$opts['template'] = 'mailchimp/'.$opts['template'];
		
		return $this->get_filtered_listing($opts);
	}*/

}
