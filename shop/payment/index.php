  <?php
   ini_set('display_errors', 1);
   ini_set('display_startup_errors', 1);
   error_reporting(E_ALL);

 include('../../perch/runtime.php');
      $success_url= 'http://localhost/perch-dev/shop/payment/stripe/success';
      $cancel_url = 'http://localhost/perch-dev/shop/payment/stripe/went/wrong';

      perch_shop_complete_payment('stripe',[
         'success_url' => $success_url,
         'cancel_url'=> $cancel_url
       ]);
 /* $success_url= '/perch-dev/shop/payment/stripe/success';
      $cancel_url = '/payment/went/wrong';
perch_shop_paypal_complete_payment($_GET,[

         'success_url' => $success_url,
         'cancel_url'=> $cancel_url
       ]);

perch_shop_klarna_confirm_payment([

                                           'success_url' => $success_url,
                                           'cancel_url'=> $cancel_url
                                         ]);*/

       ?>
