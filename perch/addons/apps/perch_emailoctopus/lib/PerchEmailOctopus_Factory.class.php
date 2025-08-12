<?php


class PerchEmailOctopus_Factory
{
	private $octopus_api_key = null;
private $octopus_api_url = null;


	protected function get_api_instance()
	{

		$Settings  = PerchSettings::fetch();
		$api_key   = $Settings->get('perch_emailoctopus_api_key')->val();
//eo_59719ee5b6b3a3d4e649547695aca1bc90254ba73c3709e0123cde03d3e5d9d5
		 $this->octopus_api_url = 'https://api.emailoctopus.com'; // Replace with the actual Octopus API endpoint
        $this->octopus_api_key = $api_key ; // Replace with your Octopus API key


		return false;
	}

public function curl_api($opts)
	{

// Initialize cURL
$this->get_api_instance();
//curl https://api.emailoctopus.com/lists/ -H "Authorization: Bearer eo_59719ee5b6b3a3d4e649547695aca1bc90254ba73c3709e0123cde03d3e5d9d5"

$ch = curl_init();
 $octopus_api_url=$this->octopus_api_url.$opts["url"];//$this->octopus_api_url."/lists/c8e227b4-b7bb-11ef-b6ef-0de94d1f09a3/contacts";

curl_setopt($ch, CURLOPT_URL, $octopus_api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
 // curl_setopt($ch,CURLOPT_POSTFIELDS , "{\"email_address\":\"otto@example.com\",\"status\":\"subscribed\"}");

curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($opts["data"]));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $this->octopus_api_key
]);
echo "data"; print_r($opts["data"]);
$err = curl_error($ch);
echo "curl_error"; print_r($err);
$response = curl_exec($ch);
echo "response"; print_r($response);
curl_close($ch);

// Handle the response from Octopus API (if needed)
if (!$err) {
   return true;
} else {
 return false;
}
}
	public function get_custom($opts)
	{
		$opts['template'] = 'emailoctopus/'.$opts['template'];
		
		return $this->get_filtered_listing($opts);
	}

}
