<?php

class PerchAPI_PerchShopProducts
{
	public function query($opts)
	{
        $opts = PerchUtil::extend([
                'skip-template' => true,
                'split-items'   => false,
                'filter'        => false,
                'paginate'      => false,
                'template'      => 'products/product',
                'api'           => true,
                'variants'      => false,
                'sort'          => 'title',
                'sort-order'    => 'ASC',
            ], $opts);

        $ShopRuntime = PerchShop_Runtime::fetch();
        $r = $ShopRuntime->get_custom('Products', $opts);

        return $r;
	}
}