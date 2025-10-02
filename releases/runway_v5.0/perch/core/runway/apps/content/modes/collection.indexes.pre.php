<?php
$API  = new PerchAPI(1.0, 'content');
$HTML = $API->get('HTML');
$Lang = $API->get('Lang');

$Paging = new PerchPaging();
$Paging->set_per_page(48);


$Collections = new PerchContent_Collections;
$Collection  = false;


// Find the collection
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id         = (int) $_GET['id'];
    $Collection = $Collections->find($id);
}

if (!$Collection || !is_object($Collection)) {
    PerchUtil::redirect(PERCH_LOGINPATH . '/core/apps/content/');
}

// App menu
if ($Collection->collectionInAppMenu()) {
    $Perch = Perch::fetch();
    $Perch->set_section('collection:' . $Collection->collectionKey());
}

$options = $Collection->get_options();

// set the current user
$Collection->set_current_user($CurrentUser->id());



$Indexes = new PerchContent_CollectionIndexes();
$indexes = $Indexes->get_by('collectionID', $Collection->id());




