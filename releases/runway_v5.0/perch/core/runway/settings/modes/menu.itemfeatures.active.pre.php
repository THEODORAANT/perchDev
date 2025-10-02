<?php

    $API    = new PerchAPI(1.0, 'core');
    $Lang   = $API->get('Lang');
    $HTML   = $API->get('HTML');


	$MenuItemFeatutes = new PerchMenuItemFeatures;
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $id = (int) $_GET['id'];
        $active=$_GET['active'];
        $MenuItemFeatute = $MenuItemFeatutes->find($id);
    }

    if (!$MenuItemFeatutes || !is_object($MenuItemFeatutes)) {
        PerchUtil::redirect(PERCH_LOGINPATH . '/core/settings/menu/features');
    }
    $parentID = $MenuItemFeatute->parentID();
    /* ---------Active MenuItemFeatute Form ----------- */

    $Form = $API->get('Form');
    $Form->set_name('active');


    if ($Form->posted() && $Form->validate()) {


		$MenuItemFeatute->featureSetActive($active);

		 if ($Form->submitted_via_ajax) {
             echo PERCH_LOGINPATH . '/core/settings/menu/features/?id='.$parentID;
             exit;
         }else{
             PerchUtil::redirect(PERCH_LOGINPATH . '/core/settings/menu/features/?id='.$parentID);
          }

    }



    $featuredetails = $MenuItemFeatute->to_array();

