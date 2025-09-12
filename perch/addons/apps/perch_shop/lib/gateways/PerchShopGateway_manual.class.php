<?php

class PerchShopGateway_manual extends PerchShopGateway_default
{

	public $payment_method    = 'authorize';

	public function handle_successful_payment($Order, $response, $gateway_opts)
	{
		$status = 'paid';
		if (isset($gateway_opts['status'])) {
			$status = $gateway_opts['status'];
		}

		$Order->finalize_as_paid($status);
		
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
}