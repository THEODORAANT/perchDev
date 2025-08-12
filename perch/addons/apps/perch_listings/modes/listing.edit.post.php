<?php
 
    echo $HTML->title_panel([
        'heading' => $heading1,
        ], $CurrentUser);
    
    if ($message) echo $message;    
    
    $template_help_html = $Template->find_help();
    if ($template_help_html) {
        echo $HTML->heading2('Help');
        echo '<div id="template-help">' . $template_help_html . '</div>';
    }
    
    echo $HTML->heading2('Listing details');
    
    echo $Form->form_start();
     $modified_details = $details;

                if (isset($modified_details['listingDescRaw'])) {
                    $modified_details['listingDescHTML'] = $modified_details['listingDescRaw'];
                }

                echo $Form->fields_from_template($Template, $modified_details);
      echo $Form->text_field('listingTitle', 'Title', isset($details['listingTitle'])?$details['listingTitle']:false);

		echo $Form->textarea_field('listingDescription', 'Description', isset($details['listingDescription'])?$details['listingDescription']:false, false, $Template->find_tag('listingDescription'));
		
        echo $Form->text_field('listingLocation', 'Location', isset($details['listingLocation'])?$details['listingLocation']:false);
        echo $Form->text_field('listingPrice', 'Price', isset($details['listingPrice'])?$details['listingPrice']:false);

        echo $Form->text_field('listingSize', 'Size (sqft)', isset($details['listingSize'])?$details['listingSize']:false);

		//echo $Form->fields_from_template($Template, $details, $Listings->static_fields);
		
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
