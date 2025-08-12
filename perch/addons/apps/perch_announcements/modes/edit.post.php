<?php
    if (is_object($Announcement)) {

            $title = $Lang->get('Editing Announcement ‘%s’', $HTML->encode($Announcement->announcementTitle()));


    }else{
        $title = $Lang->get('Creating a new Announcement');
    }

    echo $HTML->title_panel([
        'heading' => $title,
    ], $CurrentUser);

    /* ----------------------------------------- SMART BAR ----------------------------------------- */


    include('_subnav.php');

    /* ---------------------------------------- /SMART BAR ----------------------------------------- */



    $template_help_html = $Template->find_help();
    if ($template_help_html) {
        echo $HTML->heading2('Help');
        echo '<div class="template-help">' . $template_help_html . '</div>';
    }

    echo $HTML->heading2('Announcement');

    /* ---- FORM ---- */
    echo $Form->form_start('announcement-edit');
   $modified_details = $details;

            if (isset($modified_details['announcementContentRaw'])) {
                $modified_details['announcementContent'] = $modified_details['announcementContentRaw'];
            }

        echo $Form->fields_from_template($Template, $modified_details);


        echo $Form->submit_field('btnSubmit', 'Save', $API->app_path());

    echo $Form->form_end();
    /* ---- /FORM ---- */
