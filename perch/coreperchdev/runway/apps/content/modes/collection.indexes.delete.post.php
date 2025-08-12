<?php
echo $HTML->title_panel([
                            'heading' => $Lang->get('Delete Collection Index ‘%s’', PerchUtil::html($CollectionIndex->indexTitle())),
                        ]);

echo $Form->form_start();

$Alert->set('warning', $Lang->get('Are you sure you wish to delete this index and all its content?'));
echo $Alert->output();

echo $HTML->submit_bar([
                           'button' => $Form->submit('btnsubmit', 'Delete', 'button'),
                           'cancel_link' => '/core/apps/content/collections/indexes/?id='.$collectionID
                       ]);
echo $Form->form_end();
