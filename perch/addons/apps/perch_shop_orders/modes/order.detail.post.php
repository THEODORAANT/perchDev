<?php ob_start();

    $opts = $Statuses->get_select_options();

    $statuses = $Statuses->get_list(); 
    $value = 'processing';

    for($i=0; $i<count($statuses); $i++) {
        if ($statuses[$i] == $Order->orderStatus() && isset($statuses[$i+1])) {
            $value = $statuses[$i+1];
        } 
    }

    echo $HTML->title_panel([
        'heading' => $Lang->get('Viewing order'),
        'form' => [
            'action' => $Form->action(),
            'button' => $Form->select_field('status', 'Change status to', $opts, $value).$Form->submit('btnSubmit', 'Update', 'button button-small')
        ]
    ], $CurrentUser);

    include('_order_smartbar.php');
    $output2='';

    $output2.= '<div class="row logo-bckgr p-4">';

        $logo = $Settings->get('logoPath')->settingValue();
        $base = rtrim(PerchSystem::get_site_url(), '/');
        $invoice_url = $base . '/perch/addons/apps/perch_shop/assets/invoice.png';
        $line_url    = $base . '/perch/addons/apps/perch_shop/assets/line2.png';

        if ($logo) {
        $logo_url = $base . '/' . ltrim($logo, '/');
        echo '<img src="'.PerchUtil::html($logo_url).'"  class="preview" alt="" width="300" />';
            $output2.= '<img src="'.PerchUtil::html($logo_url).'"  class="preview" alt="" width="300" />';

        }

        $output2.= '<img class="float-right mt-4 ml-8" src="'.PerchUtil::html($invoice_url).'" width="200">';

    $output2.= '</div>';


    $output2.= '<div class="row">';

    $output2.= $HTML->heading2('ORDER INFO');
    $output2.= '<img src="'.PerchUtil::html($line_url).'" class="line-info">';
    
    $output2.= '<div class="inner">';
    $output2.=  '<table class="d factsheet text-uppercase">';

    $output2.=  '<tr>';
        $output2.=  '<th class="text-left">'.$Lang->get('Invoice: '). '</th>';
        $output2.=  '<td class="text-left">'.$HTML->encode($Order->orderInvoiceNumber()).'</td>';

    $output2.=  '</tr>';

    $output2.=  '<tr>';
        $output2.=  '<th class="text-left">'.$Lang->get('Order ID: ').'</th>';
        $output2.=  '<td class="text-left">'.$HTML->encode($Order->id()).'</td>';
    $output2.=  '</tr>';

    $output2.=  '<tr>';
        $output2.=  '<th class="text-left">'.$Lang->get('Received: ').'</th>';
        $output2.=  '<td class="text-left">'.$HTML->encode(PerchShop_Date::format($Order->orderCreated(), PERCH_DATE_SHORT.' '.PERCH_TIME_SHORT)).'</td>';
    $output2.=  '</tr>';

    $output2.=  '<tr>';
        $output2.=  '<th class="text-left">'.$Lang->get('Status: ').'</th>';
        $output2.=  '<td class="text-left">'.$HTML->encode(ucfirst($Order->orderStatus())).'</td>';
    $output2.=  '</tr>';

    $output2.=  '<tr>';
        $output2.=  '<th class="text-left">'.$Lang->get('Discounts: ').'</th>';
        $output2.=  '<td class="text-left">'.$HTML->encode($Currency->format_display($Order->orderDiscountsTotal())).'</td>';
    $output2.=  '</tr>';

    $output2.=  '<tr>';
        $output2.=  '<th class="text-left">'.$Lang->get('Total: ').'</th>';
        $output2.=  '<td class="text-left">'.$HTML->encode($Currency->format_display($Order->orderTotal())).'</td>';
    $output2.=  '</tr>';

    if ($Order->orderTaxID()) {
        $output2.=  '<tr>';
            $output2.=  '<th class="text-left">'.$Lang->get('Tax ID: ').'</th>';
            $output2.=  '<td class="text-left">'.$HTML->encode($Order->orderTaxID()).'</td>';
        $output2.=  '</tr>';
    }   

    $output2.=  '<tr>';
        $output2.=  '<th class="text-left">'.$Lang->get('Gateway: ').'</th>';
        $output2.=  '<td class="text-left">'.$HTML->encode($Order->orderGateway()).'</td>';
    $output2.=  '</tr>';

    $output2.=  '<tr>';
        $output2.=  '<th class="text-left w-100">'.$Lang->get('Shipping method: ').'</th>';
        $Shipping = $Order->get_shipping();
        $output2.=  '<td class="text-left">';

        if ($Shipping) {
            $output2.=  '<a href="'.$API->app_path('perch_shop').'/shippings/edit/?id='.$HTML->encode($Shipping->id()).'">'.$HTML->encode($Shipping->title()).'</a>';
        }else{
            $output2.=  $HTML->encode($Lang->get('No shipping'));
        }
        $output2.=  '</td>';
    $output2.=  '</tr>';

    $promotions = $Order->get_promotions();
    if ($promotions) {
        $output2.=  '<tr>';
            $output2.=  '<th>'.$Lang->get('Promotions').'</th>';
            $output2.=  '<td>';
            $out = [];
            foreach($promotions as $Promotion) {
                $out[] = $Promotion->title();
            }
            $output2.=  $HTML->encode(implode(', ', $out));
            $output2.=  '</td>';
        $output2.=  '</tr>';
    }

    $output2.=  '</table>';

    $output2.=  '</div>';

    // $output2.=  '</div>';




$output2.=  $HTML->heading2('CUSTOMER INFO');
$output2.= '<img src="'.PerchUtil::html($line_url).'" class="line-info">';

    $output2.=  '<div class="inner"> <table class="d factsheet text-uppercase">';

    $output2.=  '<tr>';
        $output2.=  '<th class="text-left">'.$Lang->get('Customer ID: ').'</th>';
        $output2.=  '<td class="text-left"><a href="'.$API->app_path('perch_shop_orders').'/customers/edit/?id='.$HTML->encode($Order->customerID()).'">'.$HTML->encode($Order->customerID()).'</a></td>';
    $output2.=  '</tr>';


    $output2.=  '<tr>';
        $output2.=  '<th class="text-left">'.$Lang->get('First name: ').'</th>';
        $output2.=  '<td class="text-left">'.$HTML->encode($Customer->customerFirstName()).'</td>';
    $output2.=  '</tr>';

    $output2.=  '<tr>';
        $output2.=  '<th class="text-left">'.$Lang->get('Last name: ').'</th>';
        $output2.=  '<td class="text-left">'.$HTML->encode($Customer->customerLastName()).'</td>';
    $output2.=  '</tr>';

    $output2.=  '<tr>';
        $output2.=  '<th class="text-left">'.$Lang->get('Email: ').'</th>';
        $output2.=  '<td class="text-left">'.$HTML->encode($Customer->customerEmail()).'</td>';
    $output2.=  '</tr>';

    $output2.=  '<tr>';
        $output2.=  '<th class="text-left text-uppercase w-100" >'.$Lang->get('Billing address: ').'</th>';
        $output2.=  '<td class="text-left">';
        if ($BillingAdr) {
            $output2.=  $HTML->encode($BillingAdr->addressFirstName().' '.$BillingAdr->addressLastName()).'<br>';
            $output2.= _if($BillingAdr->addressCompany(), $HTML);
            $output2.= _if($BillingAdr->get('address_1'), $HTML);
            $output2.= _if($BillingAdr->get('address_2'), $HTML);
            $output2.= _if($BillingAdr->get('city'), $HTML);
            $output2.= _if($BillingAdr->get('county'), $HTML);
            $output2.= _if($BillingAdr->get('postcode'), $HTML);
            $output2.= _if($BillingAdr->get_country_name(), $HTML);
        }else{
            $output2.=  $HTML->encode($Lang->get('No billing address'));
        }
        $output2.=  '</td>';
    $output2.=  '</tr>';

    $output2.=  '<tr>';
        $output2.=  '<th class="text-left">'.$Lang->get('Shipping address: ').'</th>';
        $output2.=  '<td class="text-left">';
        if ($ShippingAdr) {
            $output2.=  $HTML->encode($ShippingAdr->addressFirstName().' '.$ShippingAdr->addressLastName()).'<br>';
            $output2.= _if($ShippingAdr->addressCompany(), $HTML);
            $output2.= _if($ShippingAdr->get('address_1'), $HTML);
            $output2.= _if($ShippingAdr->get('address_2'), $HTML);
            $output2.= _if($ShippingAdr->get('city'), $HTML);
            $output2.= _if($ShippingAdr->get('county'), $HTML);
            $output2.= _if($ShippingAdr->get('postcode'), $HTML);
            $output2.= _if($ShippingAdr->get_country_name(), $HTML);
        }else{
            $output2.=  $HTML->encode($Lang->get('No shipping address'));
        }
        $output2.=  '</td>';
    $output2.=  '</tr>';

    $output2.=  '</table>';

    $output2.=  '</div>';

    $output2.=  '</div>';

    $output2.=  '</div>';




$output= $HTML->heading2('Order');


    $output.= '<div class="inner">';
    $output.=  '<table class="d factsheet">';

    $output.=  '<tr>';
        $output.=  '<th>'.$Lang->get('Invoice').'</th>';
        $output.=  '<td>'.$HTML->encode($Order->orderInvoiceNumber()).'</td>';
    $output.=  '</tr>';

    $output.=  '<tr>';
        $output.=  '<th>'.$Lang->get('Order ID').'</th>';
        $output.=  '<td>'.$HTML->encode($Order->id()).'</td>';
    $output.=  '</tr>';

    $output.=  '<tr>';
        $output.=  '<th>'.$Lang->get('Received').'</th>';
        $output.=  '<td>'.$HTML->encode(PerchShop_Date::format($Order->orderCreated(), PERCH_DATE_SHORT.' '.PERCH_TIME_SHORT)).'</td>';
    $output.=  '</tr>';

    $output.=  '<tr>';
        $output.=  '<th>'.$Lang->get('Status').'</th>';
        $output.=  '<td>'.$HTML->encode(ucfirst($Order->orderStatus())).'</td>';
    $output.=  '</tr>';

    $output.=  '<tr>';
        $output.=  '<th>'.$Lang->get('Discounts').'</th>';
        $output.=  '<td>'.$HTML->encode($Currency->format_display($Order->orderDiscountsTotal())).'</td>';
    $output.=  '</tr>';

    $output.=  '<tr>';
        $output.=  '<th>'.$Lang->get('Total').'</th>';
        $output.=  '<td>'.$HTML->encode($Currency->format_display($Order->orderTotal())).'</td>';
    $output.=  '</tr>';

    if ($Order->orderTaxID()) {
        $output.=  '<tr>';
            $output.=  '<th>'.$Lang->get('Tax ID').'</th>';
            $output.=  '<td>'.$HTML->encode($Order->orderTaxID()).'</td>';
        $output.=  '</tr>';
    }   

    $output.=  '<tr>';
        $output.=  '<th>'.$Lang->get('Gateway').'</th>';
        $output.=  '<td>'.$HTML->encode($Order->orderGateway()).'</td>';
    $output.=  '</tr>';

    $output.=  '<tr>';
        $output.=  '<th>'.$Lang->get('Shipping method').'</th>';
        $Shipping = $Order->get_shipping();
        $output.=  '<td>';

        if ($Shipping) {
            $output.=  '<a href="'.$API->app_path('perch_shop').'/shippings/edit/?id='.$HTML->encode($Shipping->id()).'">'.$HTML->encode($Shipping->title()).'</a>';
        }else{
            $output.=  $HTML->encode($Lang->get('No shipping'));
        }
        $output.=  '</td>';
    $output.=  '</tr>';

    $promotions = $Order->get_promotions();
    if ($promotions) {
        $output.=  '<tr>';
            $output.=  '<th>'.$Lang->get('Promotions').'</th>';
            $output.=  '<td>';
            $out = [];
            foreach($promotions as $Promotion) {
                $out[] = $Promotion->title();
            }
            $output.=  $HTML->encode(implode(', ', $out));
            $output.=  '</td>';
        $output.=  '</tr>';
    }

    $output.=  '</table>';

    $output.=  '</div>';

$output.=  $HTML->heading2('Customer');

    $output.=  '<div class="inner"> <table class="d factsheet">';

    $output.=  '<tr>';
        $output.=  '<th>'.$Lang->get('Customer ID').'</th>';
        $output.=  '<td><a href="'.$API->app_path('perch_shop_orders').'/customers/edit/?id='.$HTML->encode($Order->customerID()).'">'.$HTML->encode($Order->customerID()).'</a></td>';
    $output.=  '</tr>';


    $output.=  '<tr>';
        $output.=  '<th>'.$Lang->get('First name').'</th>';
        $output.=  '<td>'.$HTML->encode($Customer->customerFirstName()).'</td>';
    $output.=  '</tr>';

    $output.=  '<tr>';
        $output.=  '<th>'.$Lang->get('Last name').'</th>';
        $output.=  '<td>'.$HTML->encode($Customer->customerLastName()).'</td>';
    $output.=  '</tr>';

    $output.=  '<tr>';
        $output.=  '<th>'.$Lang->get('Email').'</th>';
        $output.=  '<td>'.$HTML->encode($Customer->customerEmail()).'</td>';
    $output.=  '</tr>';

    $output.=  '<tr>';
        $output.=  '<th>'.$Lang->get('Billing address').'</th>';
        $output.=  '<td>';
        if ($BillingAdr) {
            $output.=  $HTML->encode($BillingAdr->addressFirstName().' '.$BillingAdr->addressLastName()).'<br>';
            $output.= _if($BillingAdr->addressCompany(), $HTML);
            $output.= _if($BillingAdr->get('address_1'), $HTML);
            $output.= _if($BillingAdr->get('address_2'), $HTML);
            $output.= _if($BillingAdr->get('city'), $HTML);
            $output.= _if($BillingAdr->get('county'), $HTML);
            $output.= _if($BillingAdr->get('postcode'), $HTML);
            $output.= _if($BillingAdr->get_country_name(), $HTML);
        }else{
            $output.=  $HTML->encode($Lang->get('No billing address'));
        }
        $output.=  '</td>';
    $output.=  '</tr>';

    $output.=  '<tr>';
        $output.=  '<th>'.$Lang->get('Shipping address').'</th>';
        $output.=  '<td>';
        if ($ShippingAdr) {
            $output.=  $HTML->encode($ShippingAdr->addressFirstName().' '.$ShippingAdr->addressLastName()).'<br>';
            $output.= _if($ShippingAdr->addressCompany(), $HTML);
            $output.= _if($ShippingAdr->get('address_1'), $HTML);
            $output.= _if($ShippingAdr->get('address_2'), $HTML);
            $output.= _if($ShippingAdr->get('city'), $HTML);
            $output.= _if($ShippingAdr->get('county'), $HTML);
            $output.= _if($ShippingAdr->get('postcode'), $HTML);
            $output.= _if($ShippingAdr->get_country_name(), $HTML);
        }else{
            $output.=  $HTML->encode($Lang->get('No shipping address'));
        }
        $output.=  '</td>';
    $output.=  '</tr>';

    $output.=  '</table>';


    if (PerchUtil::count($items)) {

        //$output.=  $HTML->heading2('Order items');

        $output.=  '<table class="">';

        $output.=  '<thead>';
        $output.=  '<tr>';
                $output.=  '<th>'.$Lang->get('SKU').'</th>';
                $output.=  '<th>'.$Lang->get('Item').'</th>';
                $output.=  '<th>'.$Lang->get('Desc').'</th>';
                $output.=  '<th>'.$Lang->get('Qty').'</th>';
                $output.=  '<th>'.$Lang->get('Price').'</th>';
                $output.=  '<th>'.$Lang->get('Tax').'</th>';
                $output.=  '<th>'.$Lang->get('Total').'</th>';
        $output.=  '</tr>';
        $output.=  '</thead>';

        foreach($items as $Item) {
            #PerchUtil::debug($Item);
            $output.=  '<tr>';
                $output.=  '<td>'.$Item->sku().'</td>';
                $output.=  '<td>'.$Item->title().'</td>';
                $output.=  '<td>'.($Item->is_variant() ? $Item->productVariantDesc() : '').'</td>';
                $output.=  '<td>'.$Item->itemQty().'</td>';
                $output.=  '<td>'.$Item->itemPrice().'</td>';
                $output.=  '<td>'.$Item->itemTax().'</td>';
                $output.=  '<td>'.$Currency->format_display($Item->itemTotal()*$Item->itemQty()).'</td>';
            $output.=  '</tr>';
        }

        $output.=  '</table>';

    }

    $output.=  '</div>';

    $properties = PerchUtil::json_safe_decode($Order->orderDynamicFields(), true);
    if (PerchUtil::count($properties)) {
        $output.=  $HTML->heading2('Additional information');

        $output.=  '<div class="inner"><table class="d factsheet">';

        foreach($properties as $key => $val) {
            $output.=  '<tr>';
                $output.=  '<th>'.$HTML->encode($key).'</th>';
                $output.=  '<td>'.$HTML->encode($val).'</td>';
            $output.=  '</tr>';
        }

        $output.=  '</table>';

    }
    $output.=  '</div>';




    if (PerchUtil::count($items)) {

        //$output2.=  $HTML->heading2('Order items');

        $output2.=  '<div class="row text-center">';

        $output2.=  '<table class="table-b mt-4">';

        $output2.=  '<thead>';
        $output2.=  '<tr>';
                $output2.=  '<th>'.$Lang->get('SKU').'</th>';
                $output2.=  '<th>'.$Lang->get('Item').'</th>';
                $output2.=  '<th>'.$Lang->get('Desc').'</th>';
                $output2.=  '<th>'.$Lang->get('Qty').'</th>';
                $output2.=  '<th>'.$Lang->get('Price').'</th>';
                $output2.=  '<th>'.$Lang->get('Tax').'</th>';
                $output2.=  '<th>'.$Lang->get('Total').'</th>';
        $output2.=  '</tr>';
        $output2.=  '</thead>';

        foreach($items as $Item) {
            #PerchUtil::debug($Item);
            $output2.=  '<tr class="sku text-center">';
                $output2.=  '<td class="bor">'.$Item->sku().'</td>';
                $output2.=  '<td>'.$Item->title().'</td>';
                $output2.=  '<td>'.($Item->is_variant() ? $Item->productVariantDesc() : '').'</td>';
                $output2.=  '<td>'.$Item->itemQty().'</td>';
                $output2.=  '<td>'.$Item->itemPrice().'</td>';
                $output2.=  '<td>'.$Item->itemTax().'</td>';
                $output2.=  '<td>'.$Currency->format_display($Item->itemTotal()*$Item->itemQty()).'</td>';
            $output2.=  '</tr>';
        }

        $output2.=  '</table>';

        $output2.=  '</row>';

    }
    

    // $properties = PerchUtil::json_safe_decode($Order->orderDynamicFields(), true);
    // if (PerchUtil::count($properties)) {
        
    //     $output2.=  $HTML->heading2('Additional information');
        

    //     $output2.=  '<div class="inner"><table class="d factsheet">';

    //     foreach($properties as $key => $val) {
    //         $output2.=  '<tr>';
    //             $output2.=  '<th>'.$HTML->encode($key).'</th>';
    //             $output2.=  '<td>'.$HTML->encode($val).'</td>';
    //         $output2.=  '</tr>';
    //     }

    //     $output2.=  '</table>';


    // }
    
    $output2.=  '</div>';


    $output2.= '<div class="row">';

    $output2.= '<h4 class="text-center mt-5 thank-you">Thank you for your order</h4>';
    $output2.=  '</div>';


    $output2.= '<div class="row logo-bckgr p-2 footer">';

    $output2.=  '</div>';


    
     $output2.='
<style type="text/css">

.footer {
    height: 100px;
    border: 1px solid '.$headerColour.';
}

.w-100 {
    width: 200px !important;
}

.text-uppercase {
    text-transform: uppercase;
}   

.footer-h {
    color: #ffffff;
    visibility: hidden;
}

.text-center {
    text-align: center;
}

.text-right {
    text-align: center;
}

.float-right {
    float: right;
}

.thank-you {
    color: '.$headerColour.';
    text-transform: uppercase;
}

.logo-bckgr {
    background-color: '.$headerColour.';
}

.table-b {
    background-color: '.$headerColour.' !important;
    color: #ffffff !important;
    width: 100% !important;
    border-collapse: collapse !important;
}

.sku td {
    background-color: #f1f2f2 !important;
    color: #000000 !important;
    border: 1px solid #f1f2f2 !important;
}

.row {
    margin-left:-5px;
    margin-right:-5px;
  }
    
  .column {
    float: left;
    width: 80%;
    padding: 5px;
  }

  .row::after {
    content: "";
    clear: both;
    display: table;
  }
  
.p-4 {
    padding: 2rem;
}

p-2 {
    padding: 1rem;
}

.line-info {
    margin-top: -1rem;
    width: 50px;
    height: 5px;
}

.ml-8 {
    margin-left: 8rem;
}

.ml-5 {
    margin-left: 3rem;
}

.mt-5 {
    margin-top: 3rem;
}

.mt-4 {
    margin-top: 2rem;
}

.mt-3 {
    margin-top: 1.5rem;
}

.text-left {
    text-align: left;
}

.holdem {
    min-width: 40px;
}

.topadd {
    float: right;
}

form.topadd .field {
    display: inline-block;
}

form.topadd .field label {
    display: inline;
    width: auto;
}

.topadd p.submit {
    display: inline-block;
    padding: 0;
    margin: 0;
    border: 0;
}

.topadd p.submit .button {
    padding: 2px 10px;
}
</style>';

    echo $output;


    function _if($val, $HTML)
    {
        if (isset($val) && $val) {
            return $HTML->encode($val).'<br>';
        }
    }
?>



<script type="text/javascript">
function exportInPDF(){


        var data = '';
        $.ajax({
            type: 'POST',
            url: 'export_pdf/loadpdf.php',
            data: {'output2':'<?=json_encode($output2)?>'},
            xhrFields: {
                responseType: 'blob'
            },
            success: function(response){
                var blob = new Blob([response]);
                var link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = "<?=$HTML->encode($Order->orderInvoiceNumber())?>.pdf";
                link.click();
            },
            error: function(blob){
                console.log(blob);
            }
        });
}
</script>
