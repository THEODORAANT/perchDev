
<?php
    // Prevent running directly:
    if (!defined('PERCH_DB_PREFIX')) exit;


    $API = new PerchAPI(1.0, 'perch_twillio');

    $Settings = $API->get('Settings');

    if($Settings->get('perch_twillio')->val()==null){
     $Settings->set('perch_twillio', '1.0');
    }

  $sql = "
      CREATE TABLE IF NOT EXISTS `__PREFIX__twillio_dispatches` (
          `dispatchID` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `status` char(128) NOT NULL DEFAULT '',
          `customers_to` mediumtext,
          `dispatchDateTime` datetime NOT NULL DEFAULT '2015-01-01 00:00:00',
          `messageID` int(10) unsigned NOT NULL DEFAULT '0',
          PRIMARY KEY (`dispatchID`),
          KEY `idx_message` (`messageID`)
        ) CHARSET=utf8;
    CREATE TABLE IF NOT EXISTS `__PREFIX__twillio_customers` (
        `customerID` int(10) unsigned NOT NULL AUTO_INCREMENT,
        `customerFirstName` char(128) NOT NULL DEFAULT '',
        `customerLastName` char(128) NOT NULL DEFAULT '',
        `customerPhone` char(128) NOT NULL DEFAULT '',
        `customerDynamicFields` mediumtext,
        `customerCreated` datetime NOT NULL DEFAULT '2015-01-01 00:00:00',
        `customerUpdated` datetime NOT NULL DEFAULT '2015-01-01 00:00:00',
        `customerDeleted` datetime DEFAULT NULL,
        `memberID` int(10) unsigned NOT NULL DEFAULT '0',
        PRIMARY KEY (`customerID`),
        KEY `idx_member` (`memberID`)
      ) CHARSET=utf8;
    CREATE TABLE IF NOT EXISTS `__PREFIX__twillio_messages` (
      `messageID` int(11) NOT NULL AUTO_INCREMENT,
      `messageText` varchar(255) NOT NULL DEFAULT '',
      `messageDateTime` datetime DEFAULT NULL,
      `sendDateTime` datetime DEFAULT NULL,
      `messageDynamicFields` text,
      PRIMARY KEY (`messageID`),
      KEY `idx_date` (`messageDateTime`),
      FULLTEXT KEY `idx_search` (`messageText`)
    ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;";

                                                                                 $sql = str_replace('__PREFIX__', PERCH_DB_PREFIX, $sql);

                                                                                 $statements = explode(';', $sql);
                                                                                 foreach($statements as $statement) {
                                                                                     $statement = trim($statement);
                                                                                     if ($statement!='') $this->db->execute($statement);
                                                                                 }



     /*   $host = 'activation.grabaperch.com';
        $path = '/activate/v3/addons/versions/update/';
        $url = 'http://' . $host . $path;
        $data = [];
        $data['key']     = PERCH_LICENSE_KEY;
        $data['addon']     = 'perch_twillio';
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

                    $Settings->set('perch_twillio', '1.0');

			    }

			curl_close($ch);*/





?>




