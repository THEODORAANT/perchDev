<?php    

    if (is_object($Episode)) {
        $title = $Lang->get('Editing episode ‘%s’', $HTML->encode($Episode->episodeTitle()));
    }else{
        $title = $Lang->get('Creating a new episode for show ‘%s’', $HTML->encode($Show->showTitle()));
    }


    echo $HTML->title_panel([
        'heading' => $title,
    ], $CurrentUser);

    if (is_object($Show)) {

        $links = [
                [
                    'title' => $Lang->get('Shows'),
                    'link'  => $API->app_nav(),
                ],
                [
                    'title' => $Show->showTitle(),
                    'link'  => $API->app_nav().'/show/?id='.$Show->id(),
                ]
            ];

        if (is_object($Episode)) {
            $links[] = [
                'title' => $Lang->get('Episode %s', $Episode->episodeNumber()),
                'link'  => $API->app_nav().'/show/episode/?id='.$Episode->id(),
            ];
        } else {
            $links[] = [
                'title' => $Lang->get('New episode'),
                'link'  => $API->app_nav().'/show/episode/?show='.$Show->id(),
            ];
        }


        $Smartbar = new PerchSmartbar($CurrentUser, $HTML, $Lang);

        $Smartbar->add_item([
            'active' => true,
            'type'  => 'breadcrumb',
            'links' => $links
        ]);

        $Smartbar->add_item([
            'active' => false,
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



    echo $HTML->heading2('Episode details');

    $template_help_html = $Template->find_help();
    if ($template_help_html) {
        echo $HTML->heading2('Help');
        echo '<div id="template-help">' . $template_help_html . '</div>';
    }
    
    
    echo $Form->form_start();
    
        echo $Form->text_field('episodeTitle', 'Title', isset($details['episodeTitle'])?$details['episodeTitle']:false);
        echo $Form->date_field('episodeDate', 'Date', isset($details['episodeDate'])?$details['episodeDate']:false);
                        
        echo $Form->fields_from_template($Template, $details, array('episodeTitle', 'episodeSlug', 'episodeDynamicFields', 'episodeDuration', 'episodeFileSize', 'episodeTrackedURL', 'episodeDate'));

        $opts = array();
        $opts[] = array('label'=>'Draft', 'value'=>'Draft');
        $opts[] = array('label'=>'Published', 'value'=>'Published');
        echo $Form->select_field('episodeStatus', 'Status', $opts, isset($details['episodeStatus'])?$details['episodeStatus']:'Published');


        echo $HTML->heading2('Audio file');


        

        if ($Show->get_option('fileLocation')=='local') {
            echo $Form->image_field('upload', 'File', false);
            echo $Form->hidden('episodeFile', isset($details['episodeFile'])?$details['episodeFile']:'');
        }else{
            $default = false;
            $default_path = $Show->get_option('fileDefaultPath');
            if ($default_path) {
                $default = preg_replace_callback('/{([A-Za-z0-9_\-]+)}/', function($matches) use ($details, $show){
                    if (isset($details[$matches[1]])){
                        return $details[$matches[1]];
                    }
                    if (isset($show[$matches[1]])){
                        return $show[$matches[1]];
                    }
                }, $default_path);
            }
        
            echo $Form->text_field('episodeFile', 'File', isset($details['episodeFile'])?$details['episodeFile']:$default);
        }

        $default = false;
        $default_url = $Show->get_option('statsURL');
        if ($default_url) {
            $default = preg_replace_callback('/{([A-Za-z0-9_\-]+)}/', function($matches) use ($details, $show){
                if (isset($details[$matches[1]])){
                    return $details[$matches[1]];
                }

                if (isset($show[$matches[1]])){
                    return $show[$matches[1]];
                }
            }, $default_url);
        }
    
        if (isset($details['episodeTrackedURL']) && $details['episodeTrackedURL']!=''){
            $default = $details['episodeTrackedURL']; 
        }

        echo $Form->text_field('episodeTrackedURL', 'Tracked URL', $default);



        
        echo $Form->hint('hh:mm:ss - leave blank to auto-detect');
        echo $Form->text_field('episodeDuration', 'Duration', isset($details['episodeDuration'])?$Episode->duration_hms():false);



        echo $Form->hint('bytes - leave blank to auto-detect');
        echo $Form->text_field('episodeFileSize', 'File size', isset($details['episodeFileSize'])?$details['episodeFileSize']:false);






        if (is_object($Episode) && isset($details['episodeFile'])) {
            echo '<div class="field-wrap">';
            echo $Form->label('xplayer', 'Listen');
            echo $Episode->html5AudioTag();
            echo '</div>';
        }

        echo $Form->submit_field('btnSubmit', 'Save', $API->app_path());

    
    echo $Form->form_end();
    
