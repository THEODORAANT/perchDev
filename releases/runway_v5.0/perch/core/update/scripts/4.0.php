<?php
	$sql = "CREATE TABLE IF NOT EXISTS `__PREFIX__menu_item_features` (
  `featureID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parentID` int(10) unsigned NOT NULL DEFAULT '0',
  `featureType` enum('menu','app','link') NOT NULL DEFAULT 'app',
  `featureOrder` int(10) unsigned NOT NULL DEFAULT '1',
  `featureTitle` char(64) NOT NULL DEFAULT 'Unnamed feature',
  `featureValue` char(255) DEFAULT NULL,
  `featurePersists` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `featureActive` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `privID` int(10) DEFAULT NULL,
  `userID` int(10) unsigned NOT NULL DEFAULT '0',
  `featureInternal` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`featureID`)
) DEFAULT CHARSET=utf8;   ";



  $sql .= "INSERT INTO `__PREFIX__menu_item_features` (`featureID`, `parentID`, `featureType`, `featureOrder`, `featureTitle`, `featureValue`, `featurePersists`, `featureActive`, `privID`, `userID`, `featureInternal`)
VALUES
  (1,3,'app',1, 'Collections',NULL,1,0,NULL,0,0),
  (2,3,'app',2, 'Master pages',NULL,1,1,NULL,0,1),
  (3,3,'app',3, 'Navigation groups',NULL,1,1,NULL,0,1),
  (4,3,'app',4, 'Routes',NULL,1,0,NULL,0,0)";


  	$sql = str_replace('__PREFIX__', PERCH_DB_PREFIX, $sql);
  	$queries = explode(';', $sql);

  	  if (PerchUtil::count($queries) > 0) {
          foreach($queries as $query) {
              $query = trim($query);
              if ($query != '') {
                  $DB->execute($query);
                  if ($DB->errored && strpos($DB->error_msg, 'Duplicate')===false) {
                      echo '<li class="progress-item progress-alert">'.PerchUI::icon('core/face-pain').' '.PerchUtil::html(PerchLang::get('The following error occurred:')) .'</li>';
                      echo '<li class="failure"><code class="sql">'.PerchUtil::html($query).'</code></li>';
                      echo '<li class="failure"><code>'.PerchUtil::html($DB->error_msg).'</code></p></li>';
                      $errors = true;
                  }
              }
          }
      }


