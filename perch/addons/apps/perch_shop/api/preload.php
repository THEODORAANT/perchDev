<?php
	include(__DIR__ .'/../../../../runtime.php');

	$response = 'OK';

	ignore_user_abort(true);
	header('Content-Type: application/javascript');
    header("Connection: close");
    header("Content-Length: " . strlen($response));
    echo $response;
    flush();

    perch_shop_initialise_cart();
    perch_shop_cart_total([], true);