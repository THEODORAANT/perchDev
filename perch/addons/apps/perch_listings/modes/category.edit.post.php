<?php 
    
    if (is_object($Category)) {
        $title = $Lang->get('Editing ‘%s’ Category', $HTML->encode($details['categoryTitle']));
    }else{
        $title = $Lang->get('Creating a New Category');
    }
    echo $HTML->title_panel([
            'heading' => $title,
        ], $CurrentUser);
        
    if ($message) echo $message;
    

    $Smartbar = new PerchSmartbar($CurrentUser, $HTML, $Lang);

    $Smartbar->add_item([
        'active' => true,
        'type'  => 'breadcrumb',
        'links' => [
            [
                'title' => $Lang->get('Categories'),
                'link'  => $API->app_nav().'/categories/',
            ],
            [
                'title' => (is_object($Category) ? $Category->categoryTitle() : $Lang->get('New category')),
                'link'  => $API->app_nav().'/categories/edit/'.(is_object($Category) ? '?id='.$Category->id() : ''),
            ],
        ]
    ]);

    echo $Smartbar->render();


    $template_help_html = $Template->find_help();
    if ($template_help_html) {
        echo $HTML->heading2('Help');
        echo '<div id="template-help">' . $template_help_html . '</div>';
    }


    echo $HTML->heading2('Category details');
        
    
    echo $Form->form_start();
    
        echo $Form->text_field('categoryTitle', 'Title', (isset($details['categoryTitle']) ? $details['categoryTitle'] : ''));
        echo $Form->hidden('categoryID', (isset($details['categoryID']) ? $details['categoryID'] : ''));
        
        echo $Form->fields_from_template($Template, $details, $Categories->static_fields);
        

        echo $Form->submit_field('btnSubmit', 'Save', $API->app_path().'/categories/');

    
    echo $Form->form_end();
