<?php

$sql = "CREATE TABLE IF NOT EXISTS `__PREFIX__content_languages` (
 `languageID` int(10) unsigned NOT NULL AUTO_INCREMENT,
 `lang` char(32) NOT NULL DEFAULT 'en',
 `name` char(32) NOT NULL DEFAULT 'English',
 `active` tinyint(1) unsigned NOT NULL DEFAULT '1',
 `languageCreated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 PRIMARY KEY (`languageID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;  ";


    $sql .= "ALTER TABLE `__PREFIX__content_regions` ADD `language` char(255)  NOT NULL  DEFAULT '*'  AFTER `regionEditRoles`;";




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


