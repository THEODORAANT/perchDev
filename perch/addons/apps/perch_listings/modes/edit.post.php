<?php
    if (is_object($Listing)) {

            $title = $Lang->get('Editing Listing ‘%s’', $HTML->encode($Listing->listingTitle()));


    }else{
        $title = $Lang->get('Creating a new Listing');
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

    echo $HTML->heading2('Listing');

    /* ---- FORM ---- */
    echo $Form->form_start('listing-edit');
   $modified_details = $details;

            if (isset($modified_details['listingDescRaw'])) {
                $modified_details['listingDescHTML'] = $modified_details['listingDescRaw'];
            }

        echo $Form->fields_from_template($Template, $details);

		$values = array();
        $olistingspts = array();
        if(is_array($categories)) {
        	foreach($categories as $Category) {
        		$opts[] = array('label'=>$Category->categoryTitle(),'value'=>$Category->id());
        	}
        }

        echo $Form->checkbox_set('cat_ids', 'Categories', $opts, isset($details['cat_ids'])?$details['cat_ids']:array());


        echo $Form->submit_field('btnSubmit', 'Save', $API->app_path());

    echo $Form->form_end();
    /* ---- /FORM ---- */
