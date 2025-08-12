<?php
    $API    = new PerchAPI(1.0, 'core');
    $Lang   = $API->get('Lang');
    $HTML   = $API->get('HTML');


    $Pages = new PerchContent_Pages;
    $Regions = new PerchContent_Regions;
    $Page  = false;

        $Languages    = new PerchContent_Languages;
          $details = $Languages->find_all();


        // Find the page
        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $id = (int) $_GET['id'];
            $Page = $Pages->find($id);
        }

          $Form = new PerchForm('translatepage');
  if ($Form->posted() ) {
  $postvars = array('lang');



      	$data = $Form->receive($postvars);
      	//print_r($data);
      	$lang=$data["lang"];
      	$regions=$Regions->get_for_translation_page($lang,$id);

      		foreach($regions as $region) {
$newregionKey=$region->regionKey()." - ".$lang;
                              // 	print_r($newregionKey);
                               	$data=$region->to_array();
                               	unset(	$data["regionID"]);
                               	$data["regionKey"]=$newregionKey;
                               	 	//print_r($data);
                               	 $region->duplicate_region($data);

                               //	exit;

	}
    	       // PerchUtil::redirect(PERCH_LOGINPATH.'/core/apps/content/page/?id='.$id);

  }
