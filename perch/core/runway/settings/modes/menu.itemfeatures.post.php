
<?php

    echo $HTML->title_panel([
            'heading' => $Lang->get('Configuring sidebar menus'),
            'button'  => [
                        'text' => $Lang->get('Add section'),
                        'link' => '/core/settings/menu/section/edit/',
                        'icon' => 'core/plus',
                        'priv' => 'perch.menus.manage',
                    ]
        ], $CurrentUser);



    $Smartbar = new PerchSmartbar($CurrentUser, $HTML, $Lang);
    $Smartbar->add_item([
        'active'   => true,
        'title'    => 'Menus',
        'link'     => '/core/settings/menu/',
        'icon'     => 'blocks/bookmark',
    ]);
    $Smartbar->add_item([
        'active'   => false,
        'title'    => 'Reorder',
        'link'     => '/core/settings/menu/reorder/',
        'icon'     => 'core/menu',
        'position' => 'end',
    ]);
    echo $Smartbar->render();

    $Listing = new PerchAdminListing($CurrentUser, $HTML, $Lang, $Paging);

    $Listing->add_col([
            'title'     => 'Title',
            'value'     => 'featureTitle',
            'sort'      => 'featureTitle',
            'edit_link' => 'features',
        ]);
    $Listing->add_col([
            'type'      => 'status',
            'title'     => 'Active',
            'value'     => 'featureActive',
            'sort'      => 'featureActive'
        ]);
    $Listing->add_col([
            'type'      => 'action',
             'title'     => 'Set',
            'value'     => function($feature){
                if($feature->featureActive()){
                  return 'Set OFF';
                }else{
                  return 'Set ON';
                }
           },
            'display' => true,
            'class'  =>  function($feature){   if($feature->featureActive()){return 'alert';}else{ return 'success';} },
            'path'     => function($feature)  {
               return PERCH_LOGINPATH.'/core/settings/menu/features/active/?id='.$feature->featureID().'&active='.$feature->featureActive();
             }
        ]);


    echo $Listing->render($top_level_features);

