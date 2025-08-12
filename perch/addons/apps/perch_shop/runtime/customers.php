<?php

	function perch_shop_registration_form($opts=array(), $return=false)
	{
		$API  = new PerchAPI(1.0, 'perch_shop'); 

        $defaults = [];
        $defaults['template'] = 'checkout/customer_create.html';

        if (is_array($opts)) {
            $opts = array_merge($defaults, $opts);
        }else{
            $opts = $defaults;
        }

        $ShopRuntime = PerchShop_Runtime::fetch();

       	PerchSystem::set_var('country_list', PerchShop_Countries::get_list_options());

        $Template = $API->get('Template');
        $Template->set('shop/'.$opts['template'], 'shop');
        $html = $Template->render(array());
        $html = $Template->apply_runtime_post_processing($html);

        if ($return) return $html;
        echo $html;

	}

    function perch_shop_login_form($template=null, $return=false)
    {
        if (is_null($template)) {
            $template = '~perch_shop/templates/shop/checkout/customer_login.html';
        }

        return perch_member_form($template, $return);
    }


    function perch_shop_customer_edit_form($opts=array(), $return=false)
    {
        $API  = new PerchAPI(1.0, 'perch_shop'); 

        $defaults = [];
        $defaults['template'] = 'checkout/customer_update.html';

        if (is_array($opts)) {
            $opts = array_merge($defaults, $opts);
        }else{
            $opts = $defaults;
        }

        $ShopRuntime = PerchShop_Runtime::fetch();

        PerchSystem::set_var('country_list', PerchShop_Countries::get_list_options());

        $Session = PerchMembers_Session::fetch();

        $data = $Session->to_array();
        $data = array_merge($data, $ShopRuntime->get_customer_details());

        $Template = $API->get('Template');
        $Template->set('shop/'.$opts['template'], 'shop');
        $html = $Template->render($data);
        $html = $Template->apply_runtime_post_processing($html, $data);

        if ($return) return $html;
        echo $html;

    }