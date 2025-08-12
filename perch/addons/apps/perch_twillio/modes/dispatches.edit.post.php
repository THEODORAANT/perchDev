<?php

    if (is_object($Dispatch)) {
        $title = $Lang->get('Editing ‘%s’ Dispatch', $HTML->encode($details['dispatchID']));
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
                'title' => $Lang->get('Dispatches'),
                'link'  => $API->app_nav().'/dispatches/',
            ],
            [
                'title' =>'',
                'link'  => $API->app_nav().'/dispatches/edit/'.(is_object($Dispatch) ? '?id='.$Dispatch->dispatchID() : ''),
            ],
        ]
    ]);

    echo $Smartbar->render();


    $template_help_html = $Template->find_help();
    if ($template_help_html) {
        echo $HTML->heading2('Help');
        echo '<div id="template-help">' . $template_help_html . '</div>';
    }


    echo $HTML->heading2('Dispatch details');
      if (is_object($Dispatch)) {
      $dis_response=json_decode($details['response']);

       echo ' <main class="main-panel" id="main" aria-label="Content">



             <div class="inner"><table><thead>

             <tr><th><a href="/perch-dev/perch/addons/apps/perch_whatsapp/?sort=messageText">Customer</a></th>
             <th><a href="/perch-dev/perch/addons/apps/perch_whatsapp/?sort=sendDateTime">Send Status</a></th>
             <th class="action"></th><th class="action"></th></tr></thead><tbody>';
       foreach($dis_response as $cusval=>$status) {
  $findcus =  $Customers->find($cusval);
          echo '  <tr><td data-label="Message" class="">';echo $findcus->customerDynamicFields();echo '</td>
       <td data-label="Send Date" class="">';echo  $status;echo '</td>
       </tr>';

       }
    echo '</tbody></table></div></main> ';
      }else{



    echo $Form->form_start();

        echo $Form->hidden('dispatchID', (isset($details['dispatchID']) ? $details['dispatchID'] : ''));
       //  echo $Form->hidden('messageID', (isset($details['messageID']) ? $details['memberID'] : ''));
        $statusopts = array();
        $statusopts[] = array('value'=>'send', 'label'=>'send');
        $statusopts[] = array('value'=>'pending', 'label'=>'Pending');
       // $statusopts[] = array('value'=>'available', 'label'=>'Available');


       echo $Form->select_field('status', 'Status', $statusopts, (isset($details['status']) ? $details['status'] : ''));
      // echo $Form->date_field('date', 'Date', isset($details['date'])?$details['date']:false, false);
   //echo $Form->multifile_field('customersfile', "Import Contacts");
       echo $Form->select_field('messageID', 'Message', $sendto_messagesall, (isset($details['messageID']) ? $details['messageID'] : ''));
echo "
<script type='text/javascript'>

    function do_this(){

        var checkboxes = document.getElementsByName('tocustomers[]');
        var button = document.getElementById('toggle');

        if(button.checked ){
            for (var i in checkboxes){
                checkboxes[i].checked = 'FALSE';
            }
            button.checked = 'FALSE'
        }else{
            for (var i in checkboxes){
                checkboxes[i].checked = '';
            }
            button.checked = '';
        }
    }
</script>
";

       echo $Form->checkbox_set('tocustomers', 'Send To', $tocustomers, isset($details['tocustomers'])?$details['tocustomers']:array());
echo '<div class="fieldset-inner "><div class="legend-wrap"> <input type="checkbox" id="toggle"  value="Checked all" onClick="do_this()" /> Checked All</div></div>';

       echo $Form->fields_from_template($Template, $details, $Dispatches->static_fields);



        echo $Form->submit_field('btnSubmit', 'Save', $API->app_path().'/dispatches/');


    echo $Form->form_end();

}
