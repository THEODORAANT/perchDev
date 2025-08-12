<?php  
    echo $HTML->title_panel([
        'heading' => $Lang->get('Enable MultiLingual on Pages'),
        ]);


    $Smartbar = new PerchSmartbar($CurrentUser, $HTML, $Lang);

        $Smartbar->add_item([
            'active' => false,
            'title'  => '',

        ]);




    echo $Smartbar->render();


    if ($republish) {
        echo '<div class="inner">
                <ul class="progress-list">';
       // $Languages->republish_all(true);
        echo '</ul>
            </div>';
    }else{

     $opts = array();
       //print_r($Language);
     $langs=[];
     	if (PerchUtil::count($details)) {
     			foreach($details as $row) {

                      array_push($langs,$row->lang());

     			}
     			}


        $vals = $langs;

        if (!$vals) $vals = array();

        foreach($languages as $lang=>$language) {
            $opts[] = array('label'=>$language, 'value'=>$lang);
        }
        echo $Form->form_start();
        echo $HTML->wrap('div.instructions p', $Lang->get('Are you sure you wish to Enable Multi Language on  all pages?'));
//echo $Form->checkbox('All', 1, 0);
 echo $Form->checkbox_set('web_languages', 'Languages',  $opts,  $vals, $class='', $limit=false);
        echo $HTML->submit_bar([
            'button' => $Form->submit('btnsubmit', 'Enable', 'button'),
            'cancel_link' => '/core/apps/content/'
            ]);
        echo $Form->form_end();
    
    } // republish 
