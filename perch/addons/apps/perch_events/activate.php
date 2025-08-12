<?php
    // Prevent running directly:
    if (!defined('PERCH_DB_PREFIX')) exit;

    // Let's go
    $sql = "
    CREATE TABLE IF NOT EXISTS `__PREFIX__events_customers` (
      `customerID` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `customerFirstName` char(128) NOT NULL DEFAULT '',
      `customerLastName` char(128) NOT NULL DEFAULT '',
      `customerEmail` char(128) NOT NULL DEFAULT '',
      `customerDynamicFields` mediumtext,
      `customerCreated` datetime NOT NULL DEFAULT '2015-01-01 00:00:00',
      `customerUpdated` datetime NOT NULL DEFAULT '2015-01-01 00:00:00',
      `customerDeleted` datetime DEFAULT NULL,
      `memberID` int(10) unsigned NOT NULL DEFAULT '0',
      PRIMARY KEY (`customerID`),
      KEY `idx_member` (`memberID`)
    ) CHARSET=utf8;

    CREATE TABLE IF NOT EXISTS `__PREFIX__events` (
      `eventID` int(11) NOT NULL AUTO_INCREMENT,
      `eventTitle` varchar(255) NOT NULL DEFAULT '',
      `eventSlug` varchar(255) NOT NULL DEFAULT '',
      `eventDateTime` datetime DEFAULT NULL,
      `eventEndDateTime` datetime DEFAULT NULL,
      `eventDescRaw` text,
      `eventDescHTML` text,
      `eventDynamicFields` text,
      PRIMARY KEY (`eventID`),
      KEY `idx_date` (`eventDateTime`),
      FULLTEXT KEY `idx_search` (`eventTitle`,`eventDescRaw`)
    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
    
    CREATE TABLE IF NOT EXISTS `__PREFIX__events_categories` (
      `categoryID` int(11) NOT NULL AUTO_INCREMENT,
      `categoryTitle` varchar(255) NOT NULL DEFAULT '',
      `categorySlug` varchar(255) NOT NULL DEFAULT '',
      `categoryEventCount` int(10) unsigned NOT NULL DEFAULT '0',
      `categoryFutureEventCount` int(10) unsigned NOT NULL DEFAULT '0',
      `categoryDynamicFields` text,
      PRIMARY KEY (`categoryID`),
      KEY `idx_slug` (`categorySlug`)
    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
    
    CREATE TABLE IF NOT EXISTS `__PREFIX__events_to_categories` (
      `eventID` int(11) NOT NULL DEFAULT '0',
      `categoryID` int(11) NOT NULL DEFAULT '0',
      PRIMARY KEY (`eventID`,`categoryID`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED;";
    
    $sql = str_replace('__PREFIX__', PERCH_DB_PREFIX, $sql);
    
    $statements = explode(';', $sql);
    foreach($statements as $statement) {
        $statement = trim($statement);
        if ($statement!='') $this->db->execute($statement);
    }


    $API = new PerchAPI(1.0, 'perch_events');
    $UserPrivileges = $API->get('UserPrivileges');
    $UserPrivileges->create_privilege('perch_events', 'Access events');
    $UserPrivileges->create_privilege('perch_events.categories.manage', 'Manage categories');
        
    $sql = 'SHOW TABLES LIKE "'.$this->table.'"';
    $result = $this->db->get_value($sql);
    
    return $result;

