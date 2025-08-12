<?php

class PerchShopGateway_paypal_express extends PerchShopGateway_default
{
	protected $slug = 'paypal-express';
	public $omnipay_name = 'PayPal_Express';

	public function handle_successful_payment($Order, $response, $gateway_opts)
	{
		PerchUtil::debug($response, 'success');
		$Order->finalize_as_paid();
		if (isset($gateway_opts['return_url'])) {
			PerchUtil::redirect($gateway_opts['return_url']);
		}
	}

	public function handle_failed_payment($Order, $response, $gateway_opts)
	{
		$Order->set_status('payment_failed');

		if (isset($gateway_opts['cancel_url'])) {
		///	PerchUtil::redirect($gateway_opts['cancel_url']);
		}
	}

	public function set_credentials(&$Omnipay, $config)
	{
   		if ($config['test_mode']) {
   			$Omnipay->setUsername($config['test']['username']);
			$Omnipay->setPassword($config['test']['password']);
			$Omnipay->setSignature($config['test']['signature']);
			$Omnipay->setTestMode(true);
		}else{
			$Omnipay->setUsername($config['live']['username']);
			$Omnipay->setPassword($config['live']['password']);
			$Omnipay->setSignature($config['live']['signature']);
			$Omnipay->setTestMode(false);
		}
	}

	public function format_payment_options(PerchShop_Order $Order, array $opts)
	{
		$opts['transactionReference'] = $Order->id();
		return $opts;
	}

	public function get_order_from_env($Orders, $get, $post)
	{
		if (isset($get['token'])) {
			return $Orders->get_one_by('orderGatewayRef', $get['token']);
		}
	}

	public function callback_looks_valid($get, $post)
	{
		if (isset($get['token'])) {
			return true;
		}
		return false;
	}

	public function action_payment_callback($Order, $args, $gateway_opts)
	{
		$result = $this->complete_payment($Order, $args);
		PerchUtil::debug($result);
	}

	public function get_card_address($Order)
	{
		$data = $this->get_transaction_data($Order);
		
		if (PerchUtil::count($data)) {
			if (isset($data['SHIPTOCOUNTRYCODE'])) {
				return [
					'country' => $data['SHIPTOCOUNTRYCODE'],
				];
			}
		}

		return false;
	}
}
