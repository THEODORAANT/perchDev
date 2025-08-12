<?php

class PerchAPI_PerchShopCart
{
	public function query($opts=[])
	{
		$default_opts = array(
            'skip-template' => true,
            'split-items'   => false,
            'filter'        => false,
            'paginate'      => false,
            'template'      => 'shop/cart/cart.html',
            'api'           => true,
        );

        $opts = PerchUtil::extend($default_opts, $opts);

        $ShopRuntime = PerchShop_Runtime::fetch();
        $r = $ShopRuntime->get_cart_for_api($opts);

        return $r;
	}

}