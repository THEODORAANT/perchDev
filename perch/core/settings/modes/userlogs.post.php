<?php

    echo $HTML->title_panel([
        'heading' => $Lang->get('Viewing user logs'),
        ]);

    $Smartbar = new PerchSmartbar($CurrentUser, $HTML, $Lang);

    $Smartbar->add_item([
        'active' => true,
        'title'  => 'User Logs',
        'link'   => '/core/settings/userlogs/',
        'icon'   => 'core/clock',
    ]);

    echo $Smartbar->render();

	if (PerchUtil::count($user_logs)) {


        $Listing = new PerchAdminListing($CurrentUser, $HTML, $Lang, $Paging);

       $Listing->add_col([
                'title'     => 'Edited by ',
                'value'     => function($UserLog) {
                    $Users = new PerchUsers;
                    $User = $Users->find((int)$UserLog->userID());
                    return $User->userGivenName()." ".$User->userFamilyName();
                },
            ]);

        $Listing->add_col([
                'title'     => 'App',
                'value'     => 'appID',
            ]);
        $Listing->add_col([
                'title'     => 'Log Time',
                'value'     => function($UserLog){
                    return date(PERCH_DATE_SHORT.' '.PERCH_TIME_LONG, strtotime($UserLog->logTime()));
                },
            ]);
        $Listing->add_col([
                'title'     => 'Change',
                'value'    => function($UserLog) {


                 switch($UserLog->appID()) {
                      case 'content':
                        $API    = new PerchAPI(1.0, 'core');
                        $items = new PerchContent_Items($API);
                        $item = $items->find($UserLog->itemRowID());
                         $details = $item->to_array();

                          //$fields = PerchUtil::json_safe_decode($details['itemJSON'], true);


                           return "<a target='_blank' class='notification-link' href='/perch/core/apps/content/edit/?id=".$details["regionID"]."'>For region ". $details["regionID"]."</a>";
                       break;
                       case 'collections':
                          $API    = new PerchAPI(1.0, 'core');

                          $items = new PerchContent_CollectionItems($API);
                          $item = $items->find($UserLog->itemRowID());
                          $details = $item->to_array();
                          //$fields = PerchUtil::json_safe_decode($details['itemJSON'], true);

                         return "<a target='_blank'  class='notification-link' href='/perch/core/apps/content/collections/edit/?id=".$details["itemID"]."'>For collection item ". $details["itemID"]."</a>";
                         break;
                       case 'page':

                          return "<a target='_blank'  class='notification-link' href='/perch/core/apps/content/page/details/?id=".$UserLog->itemRowID()."'>For page ". $UserLog->itemRowID()."</a>";
                          break;
                       case 'categories':

                          return "<a target='_blank'  class='notification-link' href='/perch/core/apps/categories/edit/?id=".$UserLog->itemRowID()."'>For category ". $UserLog->itemRowID()."</a>";
                          break;

                     default:
                        return $UserLog->itemRowID();
                        break;
                   }
                },

            ]);

        echo $Listing->render($user_logs);
	}


