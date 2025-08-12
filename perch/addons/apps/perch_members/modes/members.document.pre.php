<?php

    $Members = new PerchMembers_Members($API);
    $MemberDocuments = new PerchMembers_Documents($API);
    $message = false;

    $Tags = new PerchMembers_Tags($API);

    $HTML = $API->get('HTML');

    if (isset($_GET['id']) && $_GET['id']!='') {
        $memberID = (int) $_GET['id'];
        $Member = $Members->find($memberID);
        $details = $Member->to_array();

        $heading1 = 'Documents a Member';

    }

      $heading2 = 'Member details';


    $Template   = $API->get('Template');
    $Template->set('members/document.html', 'documents', $MemberDocuments->default_fields);
    $Form = $API->get('Form');
    //$Form->handle_empty_block_generation($Template);
   // $Form->set_required_fields_from_template($Template, $details);



    if ($Form->submitted()) {

    }

    ?>
