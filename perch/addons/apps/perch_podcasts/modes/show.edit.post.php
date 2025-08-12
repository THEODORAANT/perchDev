<?php    
    if (is_object($Show)) {
        $title = $Lang->get('Editing options for show ‘%s’', $HTML->encode($Show->showTitle()));
    }else{
        $title = $Lang->get('Creating a new show');
    }

    echo $HTML->title_panel([
        'heading' => $title,
    ], $CurrentUser);



    if (is_object($Show)) {


        $Smartbar = new PerchSmartbar($CurrentUser, $HTML, $Lang);

        $Smartbar->add_item([
            'active' => false,
            'type'  => 'breadcrumb',
            'links' => [
                [
                    'title' => $Lang->get('Shows'),
                    'link'  => $API->app_nav(),
                ],
                [
                    'title' => $Show->showTitle(),
                    'link'  => $API->app_nav().'/show/?id='.$Show->id(),
                ]
            ]
        ]);

        $Smartbar->add_item([
            'active' => true,
            'title' => $Lang->get('Options'),
            'link'  => $API->app_nav().'/edit/?id='.$Show->id(),
            'icon'  => 'core/o-toggles',
        ]);

        $Smartbar->add_item([
            'active' => false,
            'title' => $Lang->get('Import'),
            'link'  => $API->app_nav().'/show/import/?id='.$Show->id(),
            'icon'  => 'core/inbox-download',
            'position' => 'end'
        ]);


        echo $Smartbar->render();
     
    }



    echo $HTML->heading2('Show details');

    $template_help_html = $Template->find_help();
    if ($template_help_html) {
        echo $HTML->heading2('Help');
        echo '<div id="template-help">' . $template_help_html . '</div>';
    }
    
    
    echo $Form->form_start();
    
        echo $Form->text_field('showTitle', 'Title', isset($details['showTitle'])?$details['showTitle']:false, false, 255, !isset($details['showTitle'])?' data-urlify="showSlug" ':'');
        echo $Form->text_field('showSlug', 'Slug', isset($details['showSlug'])?$details['showSlug']:false);
                
        echo $Form->fields_from_template($Template, $details, $Shows->static_fields);

        $opts = array();
        $opts[] = array('label'=>$Lang->get('Uploaded to this website'), 'value'=>'local');
        $opts[] = array('label'=>$Lang->get('Stored remotely (e.g. Amazon S3 or other file hosting service)'), 'value'=>'remote');
        echo $Form->select_field('fileLocation', 'Files for this show are', $opts, isset($options['fileLocation'])?$options['fileLocation']:false);
        
        echo $Form->hint('For files uploaded to this website');
        echo $Form->text_field('fileResourceBucket', 'Resource bucket', isset($options['fileResourceBucket'])?$options['fileResourceBucket']:'default');

        echo $Form->hint('For files stored remotely');
        echo $Form->text_field('fileDefaultPath', 'Default file location', isset($options['fileDefaultPath'])?$options['fileDefaultPath']:'https://s3.amazonaws.com/bucket/podcast-{episodeNumber}.mp3');


        $default = '';
        if (strpos($Settings->get('siteURL')->val(), ':')) {
            $default = $Settings->get('siteURL')->val();
        }else{
            $default = 'http://'.$_SERVER['HTTP_HOST'].'/';
        }

        $default .= 'podcasts/play/{showSlug}/{episodeNumber}.mp3';

        echo $Form->hint('Use the full http:// address');
        echo $Form->text_field('statsURL', 'Default tracked URL', isset($options['statsURL'])?$options['statsURL']:$default);


        echo $Form->submit_field('btnSubmit', 'Save', $API->app_path());

    
    echo $Form->form_end();
    
    

