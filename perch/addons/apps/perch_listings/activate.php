
<?php
    // Prevent running directly:
    if (!defined('PERCH_DB_PREFIX')) exit;


    $API = new PerchAPI(1.0, 'perch_listings');

    $Settings = $API->get('Settings');

    if($Settings->get('perch_listings')->val()==null){
     $Settings->set('perch_listings', '1.0');
    }



       $host = 'activation.grabaperch.com';
        $path = '/activate/v3/addons/versions/update/';
        $url = 'http://' . $host . $path;
        $data = [];
        $data['key']     = PERCH_LICENSE_KEY;
        $data['addon']     = 'perch_listings';
        $data['addonVersion'] = '1.0';

        $content = http_build_query($data);

        $result = false;
        $use_curl = false;

            PerchUtil::debug('Activating Addon via CURL');
            $ch 	= curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
            curl_setopt($ch, CURLOPT_VERBOSE, true);
			$response = curl_exec($ch);
			PerchUtil::debug($response);
			$http_status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
			if ($http_status!=200) {
			    $response = false;
			    PerchUtil::debug('Not HTTP 200: '.$http_status);
			}

			    $result=json_decode($response);

			    if(isset($result->sqlUpdates)){
			         $sql=$result->sqlUpdates;
			         $sql = str_replace('__PREFIX__', PERCH_DB_PREFIX, $sql);

                    $DB = PerchDB::fetch();

                    $statements = explode(';', $sql);
                                foreach($statements as $statement) {
                                    $statement = trim($statement);
                                    if ($statement!='') $DB->execute($statement);
                                }

                    $Settings->set('perch_listings', '1.0');

			    }

			curl_close($ch);





?>




