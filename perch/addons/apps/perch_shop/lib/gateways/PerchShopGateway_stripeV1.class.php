<?php
class PerchShopGateway_stripeV1 extends PerchShopGateway_default
{
	public function handle_successful_payment($Order, $response, $gateway_opts)
	{

		//$Order->finalize_as_paid();


       // return true;


	}

	public function handle_failed_payment($Order, $response, $gateway_opts)
	{
		$Order->set_status('payment_failed');

		if (isset($gateway_opts['cancel_url'])) {
			PerchUtil::redirect($gateway_opts['cancel_url']);
		}
	}


	public function get_api_key($config)
	{
		if ($config['test_mode'] ) {
			return $config['test']['secret_key'];
		}
		return $config['live']['secret_key'];
	}

        public function get_public_api_key($config)
        {

                if ($config['test_mode']) {
                        return $config['test']['publishable_key'];
                }
                return $config['live']['publishable_key'];
        }

        private function build_redirect_url($url)
        {
                if (filter_var($url, FILTER_VALIDATE_URL)) {
                        return $url;
                }

                $scheme = 'http';

                if ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443)) {
                        $scheme = 'https';
                }

                $host = $_SERVER['HTTP_HOST'] ?? '';
                $path = '/' . ltrim($url, '/');

                return $scheme . '://' . $host . $path;
        }
	public function get_transaction_data($Order)
	{

        $paymentIntentId=$Order->orderGatewayRef();
        $Gateway = PerchShop_Gateways::get('stripe');
            	$config = PerchShop_Config::get('gateways', $this->slug);
            	$key 	 = $Gateway->get_public_api_key($config);
            	$stripeSecretKey 	 = $Gateway->get_api_key($config);
            	 // Fetch PaymentIntent from Stripe
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, "https://api.stripe.com/v1/payment_intents/$paymentIntentId");
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_USERPWD, $stripeSecretKey . ':');

                    $response = curl_exec($ch);
                    curl_close($ch);

                    $paymentIntent = json_decode($response, true);
                    return $paymentIntent;

	}

	public function get_card_address($Order)
	{
		$data = $this->get_transaction_data($Order);

		if (isset($data['source']) && isset($data['source']['country'])) {
			return [
				'country' => $data['source']['country']
			];
		}

		return false;
	}

	public function get_exchange_rate($Order)
	{
		$this->init_native_stripe_api();
		if (strpos($Order->orderGatewayRef(), 'pi') === 0) {
           // It starts with 'pi'
             return null;

        }else{
        		$Charge = \Stripe\Charge::retrieve($Order->orderGatewayRef());

        		if ($Charge) {
        			$BalanceTransaction = \Stripe\BalanceTransaction::retrieve($Charge->balance_transaction);

        			$rate = ((float)$Charge->amount / (float)$BalanceTransaction->amount);
        			return $rate;
        		}
        }



		return null;
	}

	private function init_native_stripe_api()
	{
		$config = PerchShop_Config::get('gateways', $this->slug);
		$api_key = $this->get_api_key($config);

		\Stripe\Stripe::setApiKey($api_key);
	}

		public function get_order_from_env($Orders, $get, $post)
    	{
    		if (isset($get['session_id'])) {
    			return $Orders->get_one_by('orderGatewayRef', $get['session_id']);
    		}
    	}

	public function callback_looks_valid($get, $post)
	{
return true;
		/*if (isset($get['payment_intent'])) {
			return true;
		}
		return false;*/
	}
	public function take_payment($Order, $opts)
    {
    		$Customers = new PerchShop_Customers($this->api);
            $Customer = $Customers->find($Order->customerID());

        $orderTotal = $Order->orderTotal(); // already in pence
        $amount = (int) round($orderTotal * 100); // Convert to 12900 (pence) — GOOD

        $currency = $Order->get_currency_code(); // "gbp", "usd", etc.
        $product_name = ' Order #' . $Order->id();
            $config = PerchShop_Config::get('gateways', $this->slug);
    	//	$opts = array_merge($opts, $payment_opts);
        $stripe_secret_key = $this->get_api_key($config);

        $success_base_url = $this->build_redirect_url($opts['return_url']);
        $success_url = $success_base_url . (strpos($success_base_url, '?') === false ? '?' : '&') . 'session_id={CHECKOUT_SESSION_ID}';
        $cancel_url = $this->build_redirect_url($opts['cancel_url']);


        // Create Checkout Session via cURL
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/checkout/sessions');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_USERPWD, $stripe_secret_key . ':');

        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'payment_method_types[]' => 'card',
            //'payment_method_types[]' => 'klarna',
             'customer_email' => $Customer->customerEmail(),
            'line_items[0][price_data][currency]' => $currency,
            'line_items[0][price_data][product_data][name]' => $product_name,
            'line_items[0][price_data][unit_amount]' => $amount,
            'line_items[0][quantity]' => 1,

            'mode' => 'payment',
            'success_url' => $success_url,
            'cancel_url' => $cancel_url,

            //'billing_address_collection' => 'required',
            'customer_creation' => 'always',
            //'payment_method_options[klarna][preferred_locale]' => 'en-GB',
        ]));


        $response = curl_exec($ch);

        curl_close($ch);

        $data = json_decode($response, true);


        if (isset($data['url'])) {
            // Optional: save session ID for tracking
            $Order->set_transaction_reference($data['id']);

            // Redirect to Stripe Checkout
            echo "<script>window.location.href = '" . $data['url'] . "';</script>";
            exit;
        } else {
            echo "data";
            print_r($data);
            // Log or show error
            PerchUtil::debug('Stripe session creation failed', 'error');
            PerchUtil::debug($data, 'error');
           // return $this->handle_failed_payment($Order, null, $opts);
        }
    }

 function sendStripePayout($accountId, $amount) {
         $config = PerchShop_Config::get('gateways', $this->slug);
        	$secretKey = $this->get_api_key($config);

                  $data = http_build_query([
                      'amount' => intval($amount * 100),
                      'currency' => 'gbp',
                      'destination' => $accountId,
                      'description' => 'payout'
                  ]);

                  $ch = curl_init('https://api.stripe.com/v1/transfers');
                  curl_setopt($ch, CURLOPT_USERPWD, $secretKey . ":");
                  curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                  $response = curl_exec($ch);
                  if (curl_errno($ch)) {
                      return "Error: " . curl_error($ch);
                  }
                  curl_close($ch);
                  return $response;
              }
public function action_payment_callback($Order, $args, $opts)
	{
	if (isset($_GET['session_id'])) {
        $session_id = $_GET['session_id'];
        $config = PerchShop_Config::get('gateways', $this->slug);
       	$stripe_secret_key = $this->get_api_key($config);

        // Make cURL request to retrieve session
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.stripe.com/v1/checkout/sessions/$session_id");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $stripe_secret_key . ':');

        $response = curl_exec($ch);
       //  echo "action_response";
        //	print_r($response);
        curl_close($ch);

        $session = json_decode($response, true);
        $opts['success_url'] = $this->build_redirect_url($opts['success_url']);
        $opts['success_url'] .= (strpos($opts['success_url'], '?') === false ? '?' : '&') . 'session_id={CHECKOUT_SESSION_ID}';
        $opts['cancel_url'] = $this->build_redirect_url($opts['cancel_url']);
        // Check session payment status
        if ($session && isset($session['payment_status']) && $session['payment_status'] === 'paid') {
            // Success: mark order as paid in your system

            $transaction_reference = $session['id'] ?? $session['payment_intent'];

            if ($Order->orderGatewayRef()!=null) {
             //   $Order->set_status('paid');
                // Redirect to thank-you page or show confirmation
                //echo "Payment successful! Order marked as paid.";
              $Order->update(['orderGatewayRef'=>$session['payment_intent']]);
                 $success= $this->handle_successful_payment($Order, $response, $opts);
                           return true;

            } else {
            $this->handle_failed_payment($Order, $response, $opts);
                return false;//"Payment successful, but no matching order found.";
            }
        } else {
            echo "Payment incomplete or session invalid.";
        }
    } else {
        echo "No session_id provided.";
    }
	}


}
