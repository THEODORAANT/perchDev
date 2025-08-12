<?php

	$reqs = ['delete_priv', 'factory', 'return_path', 'title'];

	foreach($reqs as $req) {
		if (!isset($$req)) {
			die('You need to set $'.$req);
		}
	}
	
	# include the API
	include(__DIR__.'/../../../core/inc/api.php');

	$API  = new PerchAPI(1.0, 'perch_shop');
	$Lang = $API->get('Lang');
	$HTML = $API->get('HTML');
	$Paging = $API->get('Paging');

	$Factory = new $factory($API);

	# Set the page title
	$Perch->page_title = $Lang->get($title);

	# Do anything you want to do before output is started
	include('modes/_subnav.php');
	include('modes/_delete.pre.php');

	# Top layout
	include(PERCH_CORE . '/inc/top.php');

	# Display your page
	include('modes/_delete.post.php');

	# Bottom layout
	include(PERCH_CORE . '/inc/btm.php');
