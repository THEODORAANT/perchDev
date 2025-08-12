<?php


$sql = "CREATE TABLE IF NOT EXISTS `__PREFIX__user_log` (
 `logID` int(10) unsigned NOT NULL AUTO_INCREMENT,
 `appID` char(32) NOT NULL DEFAULT 'content',
 `itemFK` char(32) NOT NULL DEFAULT 'itemRowID',
 `itemRowID` int(10) unsigned NOT NULL DEFAULT 0,
 `userID` int(10) unsigned NOT NULL DEFAULT 0,
 `logTime` datetime DEFAULT NULL,
 `logCreated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 PRIMARY KEY (`logID`),
 UNIQUE KEY `idx_uni` (`appID`,`itemFK`,`itemRowID`,`userID`),
 KEY `idx_user` (`userID`),
 KEY `idx_fk` (`itemFK`,`itemRowID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;  ";






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


