  <?php


 include('../perch/runtime.php');
  /*$success_url= '/payment/success';
      $cancel_url = '/payment/went/wrong';

    perch_shop_complete_payment('stripe',[

         'success_url' => $success_url,
         'cancel_url'=> $cancel_url
       ]);*/
// perch_shop_complete_payment('worldpay');
perch_shop_revolut_complete_payment(perch_get("id"));
  if (perch_shop_order_successful()) {
      echo '<h1>Thank you for your order!</h1>';
    }else{
      echo '<h1>Sorry!</h1>';
    }
       ?>
