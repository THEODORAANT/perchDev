<?php
	
	# include the API
	include(__DIR__.'/../../../core/inc/api.php');
	
	$API  = new PerchAPI(1.0, 'perch_shop');
	$API->set_app_id_for_nav('perch_shop_products');
	$Lang = $API->get('Lang');
	$HTML = $API->get('HTML');
	$Paging = $API->get('Paging');

	# Set the page title
	$Perch->page_title = $Lang->get($title);

	//$Perch->add_javascript($API->app_path('perch_shop').'/assets/js/shop.js');
	$Perch->add_fe_plugin('shop','{"js": ["'.$API->app_path('perch_shop').'/assets/js/shop.js'.'"]}');

	include('modes/_subnav.php');
	include('modes/'.$mode.'.pre.php');
	
	# Top layout
	include(PERCH_CORE . '/inc/top.php');
	
	# Display your page
	include('modes/'.$mode.'.post.php');
	
	# Bottom layout
	include(PERCH_CORE . '/inc/btm.php');
