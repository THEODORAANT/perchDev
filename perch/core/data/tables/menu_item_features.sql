CREATE TABLE `__PREFIX__menu_item_features` (
  `featureID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parentID` int(10) unsigned NOT NULL DEFAULT '0',
  `featureType` enum('menu','app','link') NOT NULL DEFAULT 'app',
  `featureOrder` int(10) unsigned NOT NULL DEFAULT '1',
  `featureTitle` char(64) NOT NULL DEFAULT 'Unnamed item',
  `featureValue` char(255) DEFAULT NULL,
  `featurePersists` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `featureActive` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `privID` int(10) DEFAULT NULL,
  `userID` int(10) unsigned NOT NULL DEFAULT '0',
  `featureInternal` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`featureID`)
) DEFAULT CHARSET=utf8
