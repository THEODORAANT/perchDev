<?php

class PerchContent_Language extends PerchAPI_Base
{
    protected $table  = 'content_languages';
    protected $pk     = 'languageID';

    public $app_id = 'content';

 public function to_array()
    {
    	$out = parent::to_array();



        return $out;
    }

    function hasTextInOtherLanguage($str) {
        // Match for any character from any language script (non-ASCII)
        return preg_match('/[^x00-x7F]+/', $str) || preg_match('/p{L}+/u', $str);  // Matches any character beyond basic ASCII
    }

function translateto($text,$lang,$source_lang){
 $Settings   = PerchSettings::fetch();
 $deepapitestmode = $Settings->get('content_deepl_apikey_testmode')->val() ;
$authKey = $Settings->get('content_deepl_apikey')->val() ;
//$authKey = "67e52f1e-b74a-4c4a-b80f-7363354159cd"; // Replace with your key


// Initialize cURL session
$ch = curl_init();

// The URL to send the POST request to
if( $deepapitestmode ){
$url = 'https://api-free.deepl.com/v2/translate';
}else{
$url = 'https://api.deepl.com/v2/translate';
}


// The data you want to send in the POST request (as an array)
// The data you want to send in the POST request (as an array)
$data = array(
    'text' => [$text],
    'target_lang' => $lang,
    'source_lang'=> $source_lang,
);

// Convert the data array to a JSON string
$data_json = json_encode($data);

// Set cURL options
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);

// Set the Authorization header and Content-Type as application/json
$headers = array(
    "Authorization: DeepL-Auth-Key $authKey",
    "Content-Type: application/json"
);

curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

// Execute the request and capture the response
$response = curl_exec($ch);

// Output the response
//echo $response;

// Check for errors
if(curl_errno($ch)) {
    echo 'cURL Error: ' . curl_error($ch);
}

// Close cURL session
curl_close($ch);


    $decoded_response = json_decode($response, true);
print_r($decoded_response);
return $decoded_response["translations"][0]["text"];
}

    }
    ?>
