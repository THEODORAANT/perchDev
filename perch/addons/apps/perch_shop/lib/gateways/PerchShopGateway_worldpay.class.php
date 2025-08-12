<?php

class PerchShopGateway_worldpay extends PerchShopGateway_default
{
	protected $slug = 'worldpay';
	public $omnipay_name = 'WorldPay';

	public function set_credentials(&$Omnipay, $config)
	{
   		if ($config['test_mode']) {
   			$Omnipay->setInstallationId($config['test']['installationId']);
			$Omnipay->setAccountId($config['test']['accountId']);
			$Omnipay->setSecretWord($config['test']['secretWord']);
			$Omnipay->setCallbackPassword($config['test']['callbackPassword']);
			$Omnipay->setTestMode(true);
		}else{
			$Omnipay->setInstallationId($config['live']['installationId']);
			$Omnipay->setAccountId($config['live']['accountId']);
			$Omnipay->setSecretWord($config['live']['secretWord']);
			$Omnipay->setCallbackPassword($config['live']['callbackPassword']);
			$Omnipay->setTestMode(false);
		}
	}

	public function get_order_from_env($Orders, $get, $post)
	{
		if (isset($post['cartId'])) {
			$Order = $Orders->find($post['cartId']);
			if ($Order) {
				PerchShop_Session::set('shop_order_id', $Order->id());
				return $Order;
			}	
		}
		return false;
	}

	public function callback_looks_valid($get, $post)
	{
		if (isset($post['cartId']) && isset($post['transStatus']) && $post['transStatus'] == 'Y') {
			return true;
		}
		return false;
	}

	public function action_payment_callback($Order, $args, $gateway_opts)
	{
		$result = $this->complete_payment($Order, $args);
	}
}



