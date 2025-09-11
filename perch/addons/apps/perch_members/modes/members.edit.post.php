<?php

    echo $HTML->title_panel([
        'heading' => $Lang->get($heading1),
    ], $CurrentUser);

    if ($message) echo $message;    
        
    echo $HTML->heading2('Member details');
    
    echo $Form->form_start(false);
    
        //echo $Form->text_field('memberEmail', 'Email', isset($details['memberEmail'])?$details['memberEmail']:false, 'l');


        echo $Form->fields_from_template($Template, $details, array(), false);


        if (!is_object($Member)) {
            echo $Form->text_field('memberPassword', 'Password', isset($details['memberPassword'])?$details['memberPassword']:false, 'l');
        }

        /*
        echo $Form->select_field('memberStatus', 'Status', array(
                array('label'=>$Lang->get('Pending'), 'value'=>'pending'),
                array('label'=>$Lang->get('Active'), 'value'=>'active'),
                array('label'=>$Lang->get('Inactive'), 'value'=>'inactive'),

            ), isset($details['memberStatus'])?$details['memberStatus']:'active');
    */

        $state = '0';
        if (isset($details['memberStatus']) && $details['memberStatus']=='pending') {
            $state = '1';
        }

        echo $Form->hint('Will only be sent to active members');
        echo $Form->checkbox_field('send_email', 'Send welcome email', '1', $state);


        if (is_object($Member)) {


        echo $HTML->heading2('Tags');
?>
    <div class="form-inner">
        <table class="tags">
            <thead>
                <tr>
                    <th class="action">Enabled</th>
                    <th>Tag</th>
                    <th>Expires</th>
                </tr>
            </thead>
            <tbody>
        <?php
            if (PerchUtil::count($tags)) {
                foreach($tags as $Tag) {
                    echo '<tr>';
                        echo '<td class="action">'.$Form->checkbox('tag-'.$Tag->id(), '1', '1').'</td>';
                        echo '<td>'.PerchUtil::html($Tag->tag()).'</td>';
                        echo '<td>'.PerchUtil::html($Tag->tagExpires() ? date('d M Y', strtotime($Tag->tagExpires())) : '-').'</td>';
                    echo '</tr>';
                }
            }

            echo '<tr>';
                echo '<td class="action">'.$Form->label('new-tag', PerchLang::get('New')).'</td>';
                echo '<td>'.$Form->text('new-tag', false).'</td>';
                echo '<td>'.$Form->checkbox('new-expire', '1', '0').' '.$Form->datepicker('new-expires', false).'</td>';
            echo '</tr>';

        ?>

            </tbody>
        </table>
    </div>

<?php

        echo $HTML->heading2('Documents');
?>

  <div class="form-inner">
        <table class="tags">
            <thead>
                <tr>
                    <th class="action">ID</th>
                    <th>Document Name</th>
                    <th>Upload Date</th>
                     <th>Status</th>
                </tr>
            </thead>
            <tbody>
        <?php
            if (PerchUtil::count($documents)) {

            	$target_dir = PerchUtil::file_path(PERCH_RESPATH).'/perch_members/';
                foreach($documents as $Document) {
                    echo '<tr>';
                        echo '<td class="action">'.PerchUtil::html($Document->documentID()).'</td>';
                        echo '<td><a target="_blank" href="'.$target_dir.$Document->documentName().'">'.PerchUtil::html($Document->documentName()).'</a></td>';
                        echo '<td>'.PerchUtil::html($Document->documenUploadDate() ? date('d M Y', strtotime($Document->documenUploadDate())) : '-').'</td>';
                        echo '<td>'.PerchUtil::html($Document->documentDeleted() ? 'Archived' : 'Available').'</td>';
                    echo '</tr>';
                }
            }

           echo '<tr>';
                echo '<td class="action">'.$Form->label('new-document', PerchLang::get('New')).'</td>';
                  //   echo '<td>'.$Form->text('new-document', false).'</td>';
                echo '<td>'.$Form->multifile_field('new-document', false).'</td>';
               // echo '<td>'.$Form->checkbox('new-expire', '1', '0').' '.$Form->datepicker('new-expires', false).'</td>';
            echo '</tr>';

        ?>

            </tbody>
        </table>
    </div>
    <?php
          echo $HTML->heading2('Notes');

          ?>

           <div class="form-inner">
                  <table class="notes">
                      <thead>
                          <tr>

                              <th>Note</th>
                              <th>Date</th>
                                <th>Added by</th>
                          </tr>
                      </thead>
                      <tbody>
                  <?php
                      if (PerchUtil::count($notes)) {
                          foreach($notes as $Note) {
                              echo '<tr>';

                                  echo '<td>'.PerchUtil::html($Note->note()).'</td>';
                                  echo '<td>'.PerchUtil::html($Note->noteDate() ? date('d M Y', strtotime($Note->noteDate())) : '-').'</td>';
                                    echo '<td>'.PerchUtil::html($Note->addedBy()).'</td>';
                              echo '</tr>';
                          }
                      }

                      echo '<tr>';
                          echo '<td colspan="3" class="action">'.$Form->label('new-note', PerchLang::get('New'));
                          echo $Form->text('new-note', false).'</td>';

                      echo '</tr>';

                  ?>

                      </tbody>
                  </table>
              </div>

<?php
        }// is object Member

        echo $Form->submit_field('btnSubmit', 'Save', $API->app_path());
    
    echo $Form->form_end();
