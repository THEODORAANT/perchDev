<?php
$API  = new PerchAPI(1.0, 'content');
$HTML = $API->get('HTML');
$Lang = $API->get('Lang');

$Collections = new PerchContent_Collections;
$Collection  = false;

$CollectionIndexes = new PerchContent_CollectionIndexes();
$CollectionIndex   = false;
$index             = [];


// Find the index
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id              = (int) $_GET['id'];
    $CollectionIndex = $CollectionIndexes->find($id);
    if ($CollectionIndex) {
        $Collection = $Collections->find($CollectionIndex->collectionID());
    }

}

if (!$CollectionIndex) {
    if (isset($_GET['collectionID']) && is_numeric($_GET['collectionID'])) {
        $collectionID = (int) $_GET['collectionID'];
        $Collection   = $Collections->find($collectionID);

    }
}

if (!$Collection || !is_object($Collection)) {
    PerchUtil::redirect(PERCH_LOGINPATH . '/core/apps/content/');
}

// App menu
if ($Collection->collectionInAppMenu()) {
    $Perch = Perch::fetch();
    $Perch->set_section('collection:' . $Collection->collectionKey());
}

// set the current user
$Collection->set_current_user($CurrentUser->id());


/* --------- Index Form ----------- */


$Form = new PerchForm('index');

if ($Form->posted() && $Form->validate()) {
    $postvars = ['indexTitle', 'indexSlug', 'index_cols'];
    $data     = $Form->receive($postvars);

    $data['collectionID'] = (int) $Collection->id();
    if (!empty($data['indexSlug'])) {
        $data['indexSlug']    = PerchUtil::urlify($data['indexSlug']);
    }


    if (isset($data['index_cols'])) {
        $cols = $data['index_cols'];
        unset($data['index_cols']);

        $data['indexFields'] = json_encode($cols);
    }

    if ($CollectionIndex) {
        $CollectionIndex->update($data);
    } else {
        $data['indexCreated'] = gmdate('Y-m-d H:i:s');
       // echo "***";
        //print_r($data);
        $CollectionIndex      = $CollectionIndexes->create($data);
        //print_r(  $CollectionIndex  );
      //  Array ( [indexTitle] => bb [indexSlug] => bb [collectionID] => 1 [indexFields] => ["heading"] )

        //PerchUtil::redirect(PERCH_LOGINPATH . '/core/apps/content/collections/indexes/edit/?id=' . $Collection->id());
    }

    //$CollectionIndex->ensureTableIsCreated();

}

if (PerchUtil::get('created', false)) {
    $Alert->set('success', PerchLang::get('Collection index successfully created'));
}


if ($CollectionIndex) {
    $index = $CollectionIndex->to_array();
}


PerchUtil::debug($index);
