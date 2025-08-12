<?=ob_start();?>
<?php include('../../../../perch/runtime.php');?>
<?php
error_reporting(E_ALL);
error_reporting(-1);
ini_set('error_reporting', E_ALL);


?>
<?php
  // your 'success' and 'failure' URLs


  if (perch_shop_order_successful()) {
      echo '<h1>Thank you for your order!</h1>';
    }else{
      echo '<h1>Sorry!</h1>';
    }
/*
 if (perch_member_logged_in()) {
 if(!perch_twillio_is_customerphone_registered()){

       perch_twillio_registration_form();


        }elseif(!perch_twillio_customer_verified()){
        $return_url = 'http://localhost/perch-dev/shop/payment/stripe/success/verify_phonecode.php';

        perch_twillio_customer_confirmPhone_form(  [
                                                       'return_url' => $return_url

                                                     ]);
        }

 }else{
  // Returning customer login form
    perch_shop_login_form();

    // New customer sign up form
    perch_shop_registration_form();
}*/
?>
