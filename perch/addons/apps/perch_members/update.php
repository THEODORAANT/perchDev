<?php

	// Prevent running directly:
    if (!defined('PERCH_DB_PREFIX')) exit;

     $host = 'activation.grabaperch.com';
        $path = '/activate/v3/addons/versions/update/';
        $url = 'http://' . $host . $path;
        $data = [];
        $data['key']     = PERCH_LICENSE_KEY;
        $data['addon']     = 'perch_members';
        $data['addonVersion'] = '1.6.6';

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
			//echo $http_status ;
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



			    }

			curl_close($ch);


  $Settings->set('perch_members_update', '1.6.6');
