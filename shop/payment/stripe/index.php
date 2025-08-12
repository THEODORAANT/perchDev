<?=ob_start();?>
<?php include('../../../perch/runtime.php');?>
<?php
error_reporting(E_ALL);
error_reporting(-1);
ini_set('error_reporting', E_ALL);


?>
<?php

      // your 'success' and 'failure' URLs
      $success_url= 'success';
      $cancel_url = 'payment/went/wrong';

      perch_shop_complete_payment('stripe',[
         'success_url' => $success_url,
         'cancel_url'=> $cancel_url
       ]);


?>
