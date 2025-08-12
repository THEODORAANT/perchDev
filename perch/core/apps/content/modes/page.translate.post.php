<?php

	    echo $HTML->title_panel([
            'heading' => sprintf(PerchLang::get('Transalte %s Page'),' &#8216;' . PerchUtil::html($Page->pageNavText()) . '&#8217; ')
            ]);
?>
    <form method="post" action="<?php echo PerchUtil::html($Form->action()); ?>" class="form-simple">
<?php

                    echo '<h2 class="divider"><div>'.PerchLang::get('Languages').'</div></h2>';
              ?>


   <div class="field-wrap">
            <?php echo $Form->label('lang', 'Transalte page to'); ?>
            <div class="form-entry">
            <?php
                  $langs=[];
                                              	if (PerchUtil::count($details)) {
                                              			foreach($details as $row) {

                                                               array_push($langs,$row->lang());

                                              			}
                                              			}

                                   $opts = array();
                                  if (!$vals) $vals = array();

                                   if (PerchUtil::count($details)) {
                                       foreach($details as $row) {

                                           $opts[] = array('label'=>$row->lang(), 'value'=>$row->lang());
                                       }
                                   }
              echo $Form->select('lang', $opts, $val);
               echo $Form->checkbox_set('web_languages', 'Languages',  $opts,  $vals, $class='', $limit=false);

            ?>
            </div>
        </div>
       <?php

    echo $HTML->submit_bar([
        'button' =>$Form->submit('btnsubmit', 'Submit')
        ]);
?>
    </form>
