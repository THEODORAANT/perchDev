<?php

	function perch_shop_email($id, $secret=false)
	{
		$ShopRuntime = PerchShop_Runtime::fetch();
		echo $ShopRuntime->get_email_content($id, $secret);
	}