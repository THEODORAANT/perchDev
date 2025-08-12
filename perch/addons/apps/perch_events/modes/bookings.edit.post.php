<?php

    if (is_object($Booking)) {
        $title = $Lang->get('Editing ‘%s’ Booking', $HTML->encode($details['bookingID']));
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
                'title' => $Lang->get('Bookings'),
                'link'  => $API->app_nav().'/bookings/',
            ],
            [
                'title' =>'',
                'link'  => $API->app_nav().'/bookings/edit/'.(is_object($Booking) ? '?id='.$Booking->bookingID() : ''),
            ],
        ]
    ]);

    echo $Smartbar->render();


    $template_help_html = $Template->find_help();
    if ($template_help_html) {
        echo $HTML->heading2('Help');
        echo '<div id="template-help">' . $template_help_html . '</div>';
    }


    echo $HTML->heading2('Booking details');



    echo $Form->form_start();

        echo $Form->hidden('bookingID', (isset($details['bookingID']) ? $details['bookingID'] : ''));
         echo $Form->hidden('memberID', (isset($details['memberID']) ? $details['memberID'] : ''));
        $statusopts = array();
        $statusopts[] = array('value'=>'hold', 'label'=>'Hold');
        $statusopts[] = array('value'=>'confirmed', 'label'=>'Confirmed');
        $statusopts[] = array('value'=>'available', 'label'=>'Available');


       echo $Form->select_field('status', 'Status', $statusopts, (isset($details['status']) ? $details['status'] : ''));
       echo $Form->date_field('date', 'Date', isset($details['date'])?$details['date']:false, false);
        echo $Form->time_field('time', 'Time', isset($details['time'])?$details['time']:false, true);


       echo $Form->fields_from_template($Template, $details, $Bookings->static_fields);



        echo $Form->submit_field('btnSubmit', 'Save', $API->app_path().'/bookings/');


    echo $Form->form_end();

 echo $HTML->heading2('Order'); ?>
   <div class="form-inner">
         <table class="tags">
             <thead>
                 <tr>
                     <th class="action">Order Id</th>

                     <th>View</th>
                 </tr>
             </thead>
             <tbody>
               <?php

                         echo '<tr>';
                             echo '<td class="action">'.$details["order"]["orderid"].'</td>';

                             echo '<td><a href="/perch/addons/apps/perch_shop_orders/order/?id='.$details["order"]["orderid"].'">View</a></td>';
                         echo '</tr>';

                     ?>

                         </tbody>
                     </table>
                 </div>
