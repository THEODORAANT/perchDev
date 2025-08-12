<?php
	include(__DIR__.'/../../../../../runtime.php');

	perch_shop_email(perch_get('type', 'order_paid'), perch_post('secret', perch_get('secret')));