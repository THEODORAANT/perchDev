<?php

	if (PerchUtil::get('gateway')) {
		$Gateway = PerchShop_Gateways::get(PerchUtil::get('gateway'));
		$result = $Gateway->get_default_parameters();
	}