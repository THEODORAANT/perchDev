<?php include('../../../../runtime.php');?>

<?php
 file_put_contents("logs.txt", "Push received: " .  $_GET['klarna_order_id'], FILE_APPEND);

// Receive push notification from Klarna
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $klarnaOrderId = $_GET['klarna_order_id']; // Klarna sends this in the URL
    echo     $klarnaOrderId;
          //  file_put_contents("./logs.txt", "Push received: " .  $klarnaOrderId, FILE_APPEND);
perch_shop_klarna_complete_payment($klarnaOrderId);
}
?>
