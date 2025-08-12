<?php

	// Prevent running directly:
    if (!defined('PERCH_DB_PREFIX')) exit;


	$API = new PerchAPI(1.0, 'perch_emailoctopus');
	$Settings = $API->get('Settings');
    $secret = $Settings->get('perch_emailoctopus_secret')->val();
    
    if (!$secret) {
      $Settings->set('perch_emailoctopus_secret', substr(md5(uniqid()), 0, 7));
    }
     
    // Let's go
    $sql = file_get_contents(__DIR__.'/db.sql');

	$sql = str_replace('__PREFIX__', PERCH_DB_PREFIX, $sql);

    $statements = explode(';', $sql);
    foreach($statements as $statement) {
        $statement = trim($statement);
        if ($statement!='') $this->db->execute($statement);
    }

   /* $UserPrivileges = $API->get('UserPrivileges');
    $UserPrivileges->create_privilege('perch_emailoctopus', 'Access emailoctopus');
    $UserPrivileges->create_privilege('perch_emailoctopus.dash', 'Show emailoctopus on dashboard');
    $UserPrivileges->create_privilege('perch_emailoctopus.lists.edit', 'Edit list options');*/
