<?php
    if (is_object($Order)) {
        $invoice = $Order->orderInvoiceNumber();
        if ($invoice == '') $invoice = $Order->id();
        $title = $Lang->get('Editing Order ‘%s’', $HTML->encode($invoice));
    }else{
        $title = $Lang->get('Creating a New Order');
    }

    echo $HTML->title_panel([
        'heading' => $title,
    ], $CurrentUser);

    include('_orders_smartbar.php');

    if ($message) echo $message;

    $template_help_html = $Template->find_help();
    if ($template_help_html) {
        echo $HTML->heading2('Help');
        echo '<div class="template-help">' . $template_help_html . '</div>';
    }

    echo $HTML->heading2('Order');

    echo $Form->form_start('edit');

        echo $Form->fields_from_template($Template, $details);
        echo $Form->select_field('customer', 'Customer', $customer_opts, $details['customerID'] ?? '');
        echo '<script>
                var sel = document.querySelector("select[name=customer]");
                var txt = document.querySelector("input[name=customer]");
                if (sel && txt) {
                    txt.value = sel.value;
                    sel.addEventListener("change", function() {
                        txt.value = this.value;
                    });
                }
              </script>';
        echo '<a class="action" href="'.$API->app_path('perch_shop_orders').'/customers/edit/">Add new customer</a>';
        echo $Form->submit_field('btnSubmit', 'Save', $API->app_path());

    echo $Form->form_end();
