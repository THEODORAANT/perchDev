<?php

class PerchShopGateway_braintree extends PerchShopGateway_default
{
	protected $slug = 'braintree';
	public $omnipay_name = 'Braintree';

	public function handle_successful_payment($Order, $response, $gateway_opts)
	{
		$Order->finalize_as_paid();
		if (isset($gateway_opts['return_url'])) {
			PerchUtil::redirect($gateway_opts['return_url']);
		}
	}

	public function handle_failed_payment($Order, $response, $gateway_opts)
	{
		$Order->set_status('payment_failed');

		if (isset($gateway_opts['cancel_url'])) {
			PerchUtil::redirect($gateway_opts['cancel_url']);
		}
	}


	public function set_credentials(&$Omnipay, $config)
	{
   		if ($config['test_mode']) {
   			$Omnipay->setMerchantId($config['test']['merchantId']);
			$Omnipay->setPublicKey($config['test']['publicKey']);
			$Omnipay->setPrivateKey($config['test']['privateKey']);
			$Omnipay->setTestMode(true);
		}else{
			$Omnipay->setMerchantId($config['live']['merchantId']);
			$Omnipay->setPublicKey($config['live']['publicKey']);
			$Omnipay->setPrivateKey($config['live']['privateKey']);
			$Omnipay->setTestMode(false);
		}
	}

	public function get_public_api_key($config)
	{
		$Omnipay = $this->get_omnipay_instance();
		return $Omnipay->clientToken()->send()->getToken();
	}

	public function get_card_address($Order)
	{
		$Transaction = $this->get_transaction_data($Order);

		switch ($Transaction->paymentInstrumentType) {

			case 'credit_card':
				return $Transaction->creditCardDetails->countryOfIssuance;
				break;

			default:
				return $Transaction->billingDetails->countryCodeAlpha2;
				break;

		}

		return false;
	}

	public function get_transaction_data($Order)
	{
		$Omnipay = $this->get_omnipay_instance();

		$transaction = $Omnipay->find();
		$transaction->setTransactionReference($Order->orderGatewayRef());
		$response 	= $transaction->send();
		return $response->getData();
	}
}



