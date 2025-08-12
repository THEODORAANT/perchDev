<?php
    // Prevent running directly:
    if (!defined('PERCH_DB_PREFIX')) exit;

    // Let's go
    $sql = "


    CREATE TABLE IF NOT EXISTS `__PREFIX__announcements` (
      `announcementID` int(11) NOT NULL AUTO_INCREMENT,
      `announcementTitle` varchar(255) NOT NULL DEFAULT '',
      `announcementSlug` varchar(255) NOT NULL DEFAULT '',
      `announcementCreatedDate` datetime DEFAULT NULL,
       `announcementContent` text,
        `announcementContentRaw` text,
      PRIMARY KEY (`announcementID`),
      KEY `idx_date` (`announcementCreatedDate`),
      FULLTEXT KEY `idx_search` (`announcementTitle`,`announcementContent`)
    ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
    

   ";
    
    $sql = str_replace('__PREFIX__', PERCH_DB_PREFIX, $sql);
    
    $statements = explode(';', $sql);
    foreach($statements as $statement) {
        $statement = trim($statement);
        if ($statement!='') $this->db->execute($statement);
    }




