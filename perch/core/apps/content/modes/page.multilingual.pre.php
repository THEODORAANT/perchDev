<?php

	if (!$CurrentUser->has_priv('content.pages.multilingual')) {
		PerchUtil::redirect(PERCH_LOGINPATH);
	}

    $languages = [
        'en' => 'English',
        'es' => 'Spanish',
        'fr' => 'French',
        'de' => 'German',
        'it' => 'Italian',
        'pt' => 'Portuguese',
        'ru' => 'Russian',
        'zh' => 'Chinese',
        'ja' => 'Japanese',
        'ar' => 'Arabic',
        'hi' => 'Hindi',
        'ko' => 'Korean',
        'pl' => 'Polish',
        'nl' => 'Dutch',
        'sv' => 'Swedish',
        'tr' => 'Turkish',
        'no' => 'Norwegian',
        'da' => 'Danish',
        'fi' => 'Finnish'
    ];





    $API    = new PerchAPI(1.0, 'core');
    $Lang   = $API->get('Lang');
    $HTML   = $API->get('HTML');

    $Pages      = new PerchContent_Pages;

    $Languages    = new PerchContent_Languages;
      $details = $Languages->find_all();

    $republish = false;

      
    $Form = $API->get('Form');
    
    if ($Form->posted() && $Form->validate()) {
        
        if ($CurrentUser->has_priv('content.pages.multilingual')) {
        	$republish = true;
$postvars = array('web_languages');

    	$data = $Form->receive($postvars);
//print_r($data);
$Languages->create_update($data);

        	$Alert->set('success', PerchLang::get('Set the languages.'));
        	 PerchUtil::redirect(PerchUtil::html(PERCH_LOGINPATH).'/core/apps/content/multilingual/');

        }
        
    
    }
