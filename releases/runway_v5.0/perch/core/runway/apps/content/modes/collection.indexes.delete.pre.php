<?php
$API    = new PerchAPI(1.0, 'core');
$Lang   = $API->get('Lang');
$HTML   = $API->get('HTML');


if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $indexID  = (int) $_GET['id'];



    $CollectionIndexes = new PerchContent_CollectionIndexes();
    $CollectionIndex   = $CollectionIndexes->find($indexID);

    $collectionID = $CollectionIndex->collectionID();
}

if (!$CollectionIndexes || !is_object($CollectionIndexes)) {
    PerchUtil::redirect(PERCH_LOGINPATH . '/core/apps/content/collections/indexes/?id='.$collectionID);
}

/* --------- Delete Form ----------- */

$Form = $API->get('Form');
$Form->set_name('delete');

if ($Form->posted() && $Form->validate()) {

    $CollectionIndex->delete();

    if ($Form->submitted_via_ajax) {
        echo PERCH_LOGINPATH . '/core/apps/content/collections/indexes/?id='.$collectionID;
        exit;
    }else{
        PerchUtil::redirect(PERCH_LOGINPATH . '/core/apps/content/collections/indexes/?id='.$collectionID);
    }

}
