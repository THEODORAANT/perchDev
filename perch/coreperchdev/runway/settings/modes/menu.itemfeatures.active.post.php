<?php
    echo $HTML->title_panel([
            'heading' => $Lang->get('Active feature %s', PerchUtil::html($MenuItemFeatute->featureTitle())),
        ]);

    echo $Form->form_start();
    if(!$active){
      $Alert->set('success', $Lang->get('Are you sure you wish to active %s?', PerchUtil::html($MenuItemFeatute->featureTitle())));
    }else{
    $Alert->set('error', $Lang->get('Are you sure you wish to Inactive %s?', PerchUtil::html($MenuItemFeatute->featureTitle())));
    }

    echo $Alert->output();

    echo $HTML->submit_bar([
                'button' => $Form->submit('btnsubmit', 'Yes', 'button'),
                'cancel_link' => '/core/settings/menu/features/?id='.$parentID,
            ]);
    echo $Form->form_end();
