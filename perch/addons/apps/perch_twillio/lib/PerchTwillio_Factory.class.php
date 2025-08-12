<?php

require_once 'vendor/autoload.php';

use Twilio\Rest\Client;

class PerchTwillio_Factory extends PerchAPI_Factory
{
	private $api_instance = null;


	/**
	 * Get an instance of the twillio library
	 */
	protected function get_api_instance()
	{
		if (is_object($this->api_instance)) {
			return $this->api_instance;
		}
//TWILIO_ACCOUNT_SID
//TWILIO_AUTH_TOKEN
		$Settings  = PerchSettings::fetch();
		$sid   = $Settings->get('perch_twillio_sid')->val();
        $token =PERCH_TWILLIO_AUTHTOKEN;
        $fromnumber=$Settings->get('perch_twillio_FromNumber')->val();

       define('PERCH_TWILLIO_FROM',$fromnumber);
     //   $sid = getenv("TWILIO_ACCOUNT_SID");
      //  $token = getenv("TWILIO_AUTH_TOKEN");
       $twilio_instance = new Client($sid, $token);
        	//$twilio_instance = new Client("AC70bc776cf4707fce8cbdf27c4face760", "b9b26ad2b32c51034f96910d560aba5a");

		if (is_object($twilio_instance)) {
		//	$instance->verify_ssl = false;
			$this->api_instance = $twilio_instance;
			return $twilio_instance;
		}

		return false;
	}
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
