<?php
   // Prevent running directly:
    if (!defined('PERCH_DB_PREFIX')) exit;

    // Let's go
    $sql = "
    CREATE TABLE `__PREFIX__podcasts` (
	  `showID` int(10) unsigned NOT NULL AUTO_INCREMENT,
	  `showTitle` varchar(255) NOT NULL DEFAULT '',
	  `showSlug` varchar(255) NOT NULL DEFAULT '',
	  `showCreated` datetime NOT NULL,
	  `showOptions` text,
	  `showDynamicFields` longtext,
	  `showEpisodeCount` int(10) unsigned NOT NULL DEFAULT '0',
	  PRIMARY KEY (`showID`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;

	CREATE TABLE `__PREFIX__podcasts_episodes` (
	  `episodeID` int(10) unsigned NOT NULL AUTO_INCREMENT,
	  `showID` int(10) unsigned NOT NULL,
	  `episodeNumber` int(10) unsigned NOT NULL DEFAULT '0',
	  `episodeTitle` varchar(255) NOT NULL DEFAULT '',
	  `episodeSlug` varchar(255) NOT NULL DEFAULT '',
	  `episodeDate` datetime NOT NULL DEFAULT '2020-01-01 00:00:00',
	  `episodeDuration` int(10) unsigned DEFAULT '0',
	  `episodeFile` varchar(255) DEFAULT '',
	  `episodeFileSize` char(12) DEFAULT '0',
	  `episodeFileType` char(12) NOT NULL DEFAULT 'audio/mpeg',
	  `episodeDynamicFields` longtext,
	  `episodeStatus` enum('Draft','Published') NOT NULL DEFAULT 'Draft',
	  `episodeTrackedURL` varchar(255) DEFAULT '',
	  PRIMARY KEY (`episodeID`),
	  KEY `idx_show` (`showID`),
	  KEY `idx_ep` (`episodeID`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;

	CREATE TABLE `__PREFIX__podcasts_downloads` (
	  `downloadID` int(10) unsigned NOT NULL AUTO_INCREMENT,
	  `downloadDateTime` datetime NOT NULL,
	  `showID` int(10) unsigned NOT NULL DEFAULT '1',
	  `episodeID` int(10) unsigned NOT NULL,
	  `downloadUA` char(255) NOT NULL DEFAULT '',
	  PRIMARY KEY (`downloadID`),
	  KEY `idx_show` (`showID`)
	) ENGINE=InnoDB CHARSET=utf8;
    ";
    
    $sql = str_replace('__PREFIX__', PERCH_DB_PREFIX, $sql);
    
    $statements = explode(';', $sql);
    foreach($statements as $statement) {
        $statement = trim($statement);
        if ($statement!='') $this->db->execute($statement);
    }


	$API = new PerchAPI(1.0, 'perch_podcasts');
    $UserPrivileges = $API->get('UserPrivileges');
    $UserPrivileges->create_privilege('perch_podcasts', 'Access podcasts');
    $UserPrivileges->create_privilege('perch_podcasts.shows.create', 'Create new shows');
    $UserPrivileges->create_privilege('perch_podcasts.shows.delete', 'Delete shows');
    $UserPrivileges->create_privilege('perch_podcasts.episodes.import', 'Import episodes from RSS');
    $UserPrivileges->create_privilege('perch_podcasts.episodes.delete', 'Delete episodes');
    

    $sql = 'SHOW TABLES LIKE "'.$this->table.'"';
    $result = $this->db->get_value($sql);
    
    return $result;
