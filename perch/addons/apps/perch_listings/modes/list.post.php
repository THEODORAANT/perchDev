<?php
   
    echo $HTML->title_panel([
            'heading' => $Lang->get('Listings'),
            'button'  => [
                'text' => $Lang->get('Add a listing'),
                'link' => $API->app_nav().'/edit/',
                'icon' => 'core/plus',
                'priv' => 'perch_listings.create',
                ],
        ], $CurrentUser);
    
    if (isset($message)) echo $message;
    

    $Smartbar = new PerchSmartbar($CurrentUser, $HTML, $Lang);



    if (PerchUtil::count($categories)) {
        $cat_options = [];
        foreach($categories as $Category) {
            $cat_options[] = [
                    'value' => $Category->categorySlug(),
                    'title' => $Category->categoryTitle(),
                ];
        }

        $Smartbar->add_item([
            'id'      => 'cf',
            'title'   => 'By Category',
            'icon'    => 'core/o-connect',
            'active'  => PerchRequest::get('category'),
            'type'    => 'filter',
            'arg'     => 'category',
            'options' => $cat_options,
            'actions' => [

                    ],
            ]);
    }

    echo $Smartbar->render();

    if (PerchUtil::count($listings)) {

        $Listing = new PerchAdminListing($CurrentUser, $HTML, $Lang, $Paging);

        $Listing->add_col([
                'title'     => $Lang->get('Id'),
                'value'     => 'listingID',
                'sort'      => 'listingID',
                'edit_link' => 'edit',
            ]);
      $Listing->add_col([
                'title'     => $Lang->get('Title'),
                'value'     => 'listingTitle',
                'sort'      => 'listingTitle',
                'edit_link' => 'edit',
            ]);
            $Listing->add_col([
                            'title'     => $Lang->get('Location'),
                            'value'     => 'listingLocation',
                            'sort'      => 'listingLocation'

                        ]);
                                      $Listing->add_col([
                                                    'title'     => $Lang->get('Price'),
                                                    'value'     => 'listingPrice',
                                                    'sort'      => 'listingPrice'

                                                ]);
         $Listing->add_col([
                'title'     => $Lang->get('Created Date'),
                'value'     => 'listingCreated',
                'sort'      => 'listingCreated',
                'format'    => ['type'=>'date', 'format'=> PERCH_DATE_SHORT.' '.PERCH_TIME_SHORT],
            ]);



         $Listing->add_delete_action([
               'priv'   => 'perch_listings.delete',
                'inline' => true,
                'path'   => 'delete',

            ]);

        echo $Listing->render($listings);

    } // if pages
    
