<?php

    

	if (!isset($smartbar_selection)) {
		$smartbar_selection = 'details';
	}

    if (isset($message) && $smartbar_selection!='variants') {
        echo $message;
    }
	
    if (is_object($Product)) {

        $smartbar_product_id = $Product->id();

        if ($Product->is_variant()) {
            $smartbar_product_id = $Product->parentID();
        }


        $Smartbar = new PerchSmartbar($CurrentUser, $HTML, $Lang);

        $Smartbar->add_item([
            'active' => $smartbar_selection=='details',
            'title' => $Lang->get('Product Details'),
            'link'  => $API->app_nav().'/product/edit/?id='.$smartbar_product_id,
            'icon'  => 'ext/o-shirt',
        ]);

        $Smartbar->add_item([
            'active' => $smartbar_selection=='variants',
            'title' => $Lang->get('Variants'),
            'link'  => $API->app_nav().'/product/variants/?id='.$smartbar_product_id,
            'icon'  => 'ext/o-shirts',
        ]);

        $Smartbar->add_item([
            'active' => $smartbar_selection=='options',
            'title' => $Lang->get('Options'),
            'link'  => $API->app_nav().'/product/options/?id='.$smartbar_product_id,
            'icon'  => 'ext/o-ruler',
        ]);

        $Smartbar->add_item([
            'active' => $smartbar_selection=='files',
            'title' => $Lang->get('Files'),
            'link'  => $API->app_nav().'/product/files/?id='.$smartbar_product_id,
            'icon'  => 'assets/o-document',
        ]);

        $Smartbar->add_item([
            'active' => $smartbar_selection=='tags',
            'title' => $Lang->get('Tags'),
            'link'  => $API->app_nav().'/product/tags/?id='.$smartbar_product_id,
            'icon'  => 'core/o-tag',
        ]);

        echo $Smartbar->render();








        // echo $HTML->smartbar(
        //         // $HTML->smartbar_link(($smartbar_selection=='details'), 
        //         //             array( 
        //         //                 'link'=> $API->app_path('perch_shop_products').'/product/edit/?id='.$smartbar_product_id,
        //         //                 'label' => $Lang->get('Product Details'),
        //         //             )
        //         //         ),
        //         // $HTML->smartbar_link(($smartbar_selection=='variants'), 
        //         //         array( 
        //         //             'link'=> $API->app_path('perch_shop_products').'/product/variants/?id='.$smartbar_product_id,
        //         //             'label' => $Lang->get('Variants'),
        //         //         )
        //         //     ),
        //         // $HTML->smartbar_link(($smartbar_selection=='options'), 
        //         //         array( 
        //         //             'link'=> $API->app_path('perch_shop_products').'/product/options/?id='.$smartbar_product_id,
        //         //             'label' => $Lang->get('Options'),
        //         //         )
        //         //     ),
        //         // $HTML->smartbar_link(($smartbar_selection=='files'), 
        //         //         array( 
        //         //             'link'=> $API->app_path('perch_shop_products').'/product/files/?id='.$smartbar_product_id,
        //         //             'label' => $Lang->get('Files'),
        //         //         )
        //         //     ),
        //         // $HTML->smartbar_link(($smartbar_selection=='tags'), 
        //         //         array( 
        //         //             'link'=> $API->app_path('perch_shop_products').'/product/tags/?id='.$smartbar_product_id,
        //         //             'label' => $Lang->get('Tags')
        //         //         )
        //         //     )
        //     );

    }

