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

/*public function take_payment($Order, $opts)
	{
		$payment_opts = [
				'amount'        => $Order->orderTotal(),
				'currency'      => $Order->get_currency_code(),
				'transactionId' => $Order->id(),
				'clientIp'		=> PerchUtil::get_client_ip(),
				'description'	=> 'Order #'.$Order->id(),
		    ];


		// optionally get the payment card (usually just customer details, not card numbers)
		$card = $this->get_payment_card($Order);
		if ($card) {
			$payment_opts['card'] = $card;
		}
        $config = PerchShop_Config::get('gateways', $this->slug);
		$opts = array_merge($opts, $payment_opts);

		$opts = $this->format_payment_options($Order, $opts);

		// Send purchase request
		if( $opts['confirm']){
		$payment_method = $this->authorize_method;
		$Omnipay = $this->get_omnipay_intents_instance();

		}else{
		$payment_method = $this->payment_method;
		$Omnipay = $this->get_omnipay_instance();
		}



    	$response = $Omnipay->$payment_method($opts)->send();
		// Process response
		if ($response->isSuccessful()) {

			$Order->set_transaction_reference($response->getTransactionReference());

		    // Payment was successful
		    PerchUtil::debug('Payment successful');
		    return $this->handle_successful_payment($Order, $response, $opts);

		} elseif ($response->isRedirect()) {
		     $paymentIntentReference = $response->getPaymentIntentReference();

			$Order->set_transaction_reference($paymentIntentReference);//$response->getTransactionReference());
			$this->store_data_before_redirect($Order, $response, $opts);

		    // Redirect to offsite payment gateway
		    PerchUtil::debug('Payment redirect response');
		    $response->redirect();

		} else {

		    // Payment failed
		    PerchUtil::debug('Payment failed', 'error');
		    PerchUtil::debug($response, 'error');
		    return $this->handle_failed_payment($Order, $response, $opts);
		}
	}*/
	public function take_payment($Order, $opts)
    {
        // Merge options with default payment metadata
        $payment_opts = [
            'amount'        => $Order->orderTotal(),
            'currency'      => $Order->get_currency_code(),
            'transactionId' => $Order->id(),
            'clientIp'      => PerchUtil::get_client_ip(),
            'description'   => 'Manual Payment for Order #' . $Order->id(),
        ];

        $opts = array_merge($opts, $payment_opts);

        // Optional: log or simulate a transaction reference
        $manualTransactionRef = 'MANUAL-' . $Order->id() . '-' . time();
        $Order->set_transaction_reference($manualTransactionRef);

        PerchUtil::debug('Manual payment: marking order as paid.');

        // Handle manual payment success
        if ($this->handle_successful_payment($Order, null, $opts)) {
            // Redirect to success or return URL
            if (isset($opts['success_url'])) {
                PerchUtil::redirect($opts['success_url']);
            }

            if (isset($opts['return_url'])) {
                PerchUtil::redirect($opts['return_url']);
            }
        }

        return;
    }


	public function handle_failed_payment($Order, $response, $gateway_opts)
	{
		$Order->set_status('payment_failed');

		if (isset($gateway_opts['cancel_url'])) {
			PerchUtil::redirect($gateway_opts['cancel_url']);
		}
	}
}
