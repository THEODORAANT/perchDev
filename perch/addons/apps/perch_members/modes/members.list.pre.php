<?php

    // Try to update
    $Settings = $API->get('Settings');

    if ($Settings->get('perch_members_update')->val()!='1.6.5') {
        include('update.php');
    }




    $HTML = $API->get('HTML');

    $Members = new PerchMembers_Members($API);

    $Paging = $API->get('Paging');
    $Paging->set_per_page(40);

    $Tags = new PerchMembers_Tags($API);
    $tags = $Tags->all();


	$Lang = $API->get('Lang');

    $members = array();

    /****Filtering template****/
    $Template   = $API->get('Template');
    $Template->set('members/filter.html', 'members');

    $Form = $API->get('Form');
    $Form->handle_empty_block_generation($Template);

        if ($Form->submitted()) {

               $post = $_POST;


               //$postvars = array('memberEmail', 'memberStatus');

           	//$data = $Form->receive($postvars);

               $data = $Form->get_posted_content($Template, $Members, false, false);
               $filerdata= json_encode($data);
               // $details=$filerdata;

                $details=$data["memberProperties"];
                 $details =json_decode($details, TRUE);

               //print_r( $filerdata);
              $filter="memberProperties";
              $status = 'all';
               // PerchUtil::debug($data);
          }else{
             $details = array();
                 $pending_mod_count = $Members->get_count('pending');

                 if ($pending_mod_count>0) {
                     $filter = 'all';
                     $status = 'pending';
                 }else{
                     $filter = 'status';
                     $status = 'all';
                 }
          }
    //print_r( $details);
  $Form->set_required_fields_from_template($Template, $details);





if($filter!="memberProperties"){


    if (isset($_GET['tag']) && $_GET['tag'] != '') {
        $filter = 'tag';
        $tag = $_GET['tag'];
        $status = '';
    }


    if (isset($_GET['email']) && $_GET['email'] != '') {
        $filter = 'email';
        $email = $_GET['email'];
        $status = '';
    }



    if (isset($_GET['status']) && $_GET['status'] != '') {
        $filter = 'status';
        $status = $_GET['status'];
    }

    if (isset($_GET['show-filter']) && $_GET['show-filter'] != '') {
        $status = '';
    }
    }

 $sort=false;
if (isset($_GET['sort']) && $_GET['sort'] != '') {
        $sort = $_GET['sort'];

        }
    switch ($filter) {

        case 'tag':
            $members = $Members->get_by_tag_for_admin_listing($tag);
            break;

        case 'email':
            $members = $Members->get_by_email($email);
            break;

        case 'status':
            if ($status == 'all') {
                $members = $Members->all($Paging);
            }else{
                $members = $Members->get_by_status($status,$sort, $Paging);
            }
             break;
        case 'memberProperties':
          $members = $Members->get_by_properties($filerdata);
          break;


        default:
            $members = $Members->get_by_status('pending');


            break;
    }

    // Install
    if ($status=='all' && $members == false) {
        $Members->attempt_install();
    }
