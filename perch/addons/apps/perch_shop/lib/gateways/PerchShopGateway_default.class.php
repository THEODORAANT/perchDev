<?php

use Omnipay\Omnipay;
use Omnipay\Common\CreditCard;

class PerchShopGateway_default
{
	protected $api;
	protected $slug = 'default';
	public $omnipay_name = null;

	public $payment_method    = 'purchase';
	public $authorize_method    = 'authorize';
	public $completion_method = 'completePurchase';

	public function __construct($api, $slug='default')
	{
		$this->api = $api;
		$this->slug = $slug;
		if (is_null($this->omnipay_name)) {
			$this->omnipay_name = ucfirst($slug);
		}

	}

	public function get_default_parameters()
	{
		$Omnipay = Omnipay::create($this->omnipay_name);
		return $Omnipay->getDefaultParameters();
	}



	public function take_payment($Order, $opts)
	{ //echo "take_payment";
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
		if( isset($opts['confirm'])){

		$payment_method = $this->authorize_method;
		$Omnipay = $this->get_omnipay_intents_instance();

		}else{

		$payment_method = $this->payment_method;
		$Omnipay = $this->get_omnipay_instance();
		}
  //print_r($Omnipay);
	//echo "response";
	  try{

    	$response = $Omnipay->$payment_method($opts)->send();
      //print_r($response);

         }catch(Exception $e) {
          // echo 'Message: ' .$e->getMessage();
           if (isset($opts['cancel_url'])) {
              PerchUtil::redirect($opts['cancel_url']);
             }
             //print_r($opts);

             //echo "Payment Failed";
              return $this->handle_failed_payment($Order, null, $opts);
         }
    			// Process response
		if ($response->isSuccessful()) {

        //old strpe
        if(method_exists($response, 'getPaymentIntentReference') && $response->getPaymentIntentReference()!=null){

        $paymentIntentReference = $response->getPaymentIntentReference();
        }else{

          $paymentIntentReference =$response->getTransactionReference();
        }


			 $Order->set_transaction_reference($paymentIntentReference);


		    // Payment was successful
		    PerchUtil::debug('Payment successful');

            if($this->handle_successful_payment($Order, $response, $opts)){
                if (isset($opts['success_url'])) {
                       PerchUtil::hold_redirects();
                        PerchUtil::redirect($opts['success_url']);
                    }
                            if (isset($opts['return_url'])) {
                            //  echo "getCaptureMethod"; echo $response->getCaptureMethod();
                               if($response->getCaptureMethod()=='manual'){
                                               $response_capture =$this->capture_stripe_payment($paymentIntentReference);
                                               }
                             // PerchUtil::hold_redirects();
                            //  echo "redirect";
                            PerchUtil::redirect($opts['return_url']."?confirm=false&payment_intent=".$paymentIntentReference);


                            }
            }

		    return ;


		} elseif ($response->isRedirect()) {

            if( isset($opts['confirm'])){
		        $paymentIntentReference = $response->getPaymentIntentReference();

			    $Order->set_transaction_reference($paymentIntentReference);//$response->getTransactionReference());
			}else{


            			$Order->set_transaction_reference($response->getTransactionReference());
			}

			$this->store_data_before_redirect($Order, $response, $opts);

		    // Redirect to offsite payment gateway
		    PerchUtil::debug('Payment redirect response');
		    PerchUtil::debug($response);

		    if (!PerchUtil::get_hold_redirects()) {
	         $response->redirect();
		    }



		} else {

		    // Payment failed
		    PerchUtil::debug('Payment failed', 'error');
		    PerchUtil::debug($response,  'error');
		    return $this->handle_failed_payment($Order, $response, $opts);
		}
	}

	public function capture_stripe_payment($payment_intent){
		$config = PerchShop_Config::get('gateways', $this->slug);

        $Omnipay = Omnipay::create($this->omnipay_name.'\PaymentIntents');
         $this->set_credentials($Omnipay, $config);
	      $response_capture = $Omnipay->capture([
                                'paymentIntentReference' => $payment_intent
                            ])->send();
              	   if ($response_capture->isSuccessful()) {
              	   return true;
              	   }
              	   return false;

	}

	public function confirm_payment($Order, $opts, $gateway_opts){
            //echo "confirm_payment ";
		$payment_opts = [
		        'amount'   => $Order->orderTotal(),
		        'currency' => $Order->get_currency_code(),
		    ];
        $payment_opts = array_merge($payment_opts, $gateway_opts);
		$opts = array_merge($opts, $payment_opts);

		$opts = $this->format_payment_options($Order, $opts);
		if(isset($opts["confirm"]) && $opts["confirm"]=="false"){

            $Order->finalize_as_paid();

			if (isset($opts['success_url'])) {

                                   // PerchUtil::redirect($opts['success_url']);
                   echo("<script>location.href = '".$opts['success_url']."';</script>");

                                    return ;
                                }

		}else{

		$config = PerchShop_Config::get('gateways', $this->slug);

        		$Omnipay = Omnipay::create($this->omnipay_name.'\PaymentIntents');
        		$this->set_credentials($Omnipay, $config);

        		$payment_method = 'confirm';//$this->completion_method;


        	 try{
                $response = $Omnipay->confirm([
                            'paymentIntentReference' => $opts['payment_intent'],
                            'returnUrl' =>$opts['success_url'],
                             'capture_method'=> 'automatic'
                 ])->send();

                 }catch(Exception $e) {
                  // echo 'Message: ' .$e->getMessage();
                   if (isset($opts['cancel_url'])) {
                      PerchUtil::redirect($opts['cancel_url']);
                     }
                     }
               //  echo "confirm";print_r($response);
             //   echo "getCaptureMethod"; echo $response->getCaptureMethod();
                if($response->getCaptureMethod()=='manual'){
                 $response_capture =$this->capture_stripe_payment($opts['payment_intent']);
        	      /* $response_capture = $Omnipay->capture([
                            'paymentIntentReference' => $opts['payment_intent']
                        ])->send();*/
            //echo "response_capture in"; print_r($response_capture );
        		// Process response
        	   if ($response_capture) {

        	    PerchUtil::debug('Payment successful');
            	//$Order->update(['orderGatewayRef'=>$response->getTransactionReference()]);
            	//echo "Order in"; print_r($Order );echo "opts in"; print_r($opts );

            	$Order->finalize_as_paid();
                //$successresult= $this->handle_successful_payment($Order, $response, $opts);


                if (isset($opts['success_url'])) {

                                                   // PerchUtil::redirect($opts['success_url']);
                                    echo("<script>location.href = '".$opts['success_url']."';</script>");

                                                    return ;
                                                }

        		} else {
                  $successresult=  $this->handle_failed_payment($Order, $response_capture, $gateway_opts);
        		    // Payment failed
        		    PerchUtil::debug('Payment failed', 'error');

        		       if (isset($opts['cancel_url'])) {

                                                                       // PerchUtil::redirect($opts['success_url']);
                                                        echo("<script>location.href = '".$opts['cancel_url']."';</script>");

                                                                        return ;
                                                                    }

        		}
		}


    }


		return false;
	}

	public function complete_payment($Order, $opts)
	{


		$payment_opts = [
		        'amount'   => $Order->orderTotal(),
		        'currency' => $Order->get_currency_code(),
		    ];

		$opts = array_merge($opts, $payment_opts);

		$opts = $this->format_payment_options($Order, $opts);

		$config = PerchShop_Config::get('gateways', $this->slug);

		$Omnipay = Omnipay::create($this->omnipay_name);
		$this->set_credentials($Omnipay, $config);

		$payment_method = $this->completion_method;


		$response = $Omnipay->$payment_method($opts)->send();


		// Process response
		if ($response->isSuccessful()) {

		    // Payment was successful
		    PerchUtil::debug('Payment successful');
		    $Order->update(['orderGatewayRef'=>$response->getTransactionReference()]);
		    return $this->handle_successful_payment($Order, $response, $opts);

		} elseif ($response->isRedirect()) {

			$Order->set_transaction_reference($response->getTransactionReference());
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

		return false;
	}

	public function get_api_key($config)
	{

		if ($config['test_mode']) {
			return 'Bearer '.$config['test']['api_key'];
		}
		return 'Bearer '.$config['live']['api_key'];
	}

	public function get_public_api_key($config)
	{
		return false;
	}

	public function format_payment_options(PerchShop_Order $Order, array $opts)
	{
		return $opts;
	}

	public function produce_payment_response(array $args, array $gateway_opts)
	{
		return;
	}

	public function get_order_from_env($Orders, $get, $post)
	{
		return false;
	}

	public function callback_looks_valid($get, $post)
	{
		return false;
	}

	public function get_callback_args($get, $post)
	{
		return $get;
	}

	public function action_payment_callback($Order, $args, $gateway_opts)
	{
		return true;
	}

	public function finalize_as_paid($Order)
	{
		return true;
	}

	public function handle_successful_payment($Order, $response, $gateway_opts)
	{
		$Order->finalize_as_paid();

		return $response;
	}

	public function handle_failed_payment($Order, $response, $gateway_opts)
	{
		$Order->set_status('payment_failed');
		echo $response->getMessage();
		return false;
	}

	public function set_credentials(&$Omnipay, $config)
	{
		$api_key = $this->get_api_key($config);

		if ($api_key) {
			$Omnipay->setApiKey($api_key);
		}
	}

	public function store_data_before_redirect($Order, $response, $opts)
	{

	}

	public function get_card_address($Order)
	{
		return false;
	}

	public function get_omnipay_intents_instance()
    {

    	$config = PerchShop_Config::get('gateways', $this->slug);
    	$Omnipay = Omnipay::create($this->omnipay_name.'\PaymentIntents');
    	$this->set_credentials($Omnipay, $config);

    	return $Omnipay;
    }

	public function get_omnipay_instance()
	{

		$config = PerchShop_Config::get('gateways', $this->slug);
		$Omnipay = Omnipay::create($this->omnipay_name);
		$this->set_credentials($Omnipay, $config);
		return $Omnipay;
	}

	public function get_transaction_data($Order)
	{
		$Omnipay = $this->get_omnipay_instance();


		if (str_starts_with($Order->orderGatewayRef(), 'pi_')) {
             $response = $Omnipay->fetchTransaction(['transactionReference' => $Order->orderGatewayRef()]);
        } else {
        	$transaction = $Omnipay->fetchTransaction();

        		$transaction->setTransactionReference($Order->orderGatewayRef());
        		$response 	= $transaction->send();

        }
		return $response->getData();
	}

	public function get_payment_card($Order)
	{
		$Customers = new PerchShop_Customers($this->api);
        $Customer = $Customers->find($Order->customerID());

        $Addresses = new PerchShop_Addresses($this->api);

        $ShippingAddr = $Addresses->find((int)$Order->orderShippingAddress());
        $BillingAddr  = $Addresses->find((int)$Order->orderBillingAddress());

		$data = [
			'firstName'        => $Customer->customerFirstName(),
			'lastName'         => $Customer->customerLastName(),
			'billingAddress1'  => $BillingAddr->get('address_1'),
			'billingAddress2'  => $BillingAddr->get('address_2'),
			'billingCity'      => $BillingAddr->get('city'),
			'billingPostcode'  => $BillingAddr->get('postcode'),
			'billingState'     => $BillingAddr->get('county'),
			'billingCountry'   => $BillingAddr->get_country_iso2(),
			'shippingAddress1' => $ShippingAddr->get('address_1'),
			'shippingAddress2' => $ShippingAddr->get('address_2'),
			'shippingCity'     => $ShippingAddr->get('city'),
			'shippingPostcode' => $ShippingAddr->get('postcode'),
			'shippingState'    => $ShippingAddr->get('county'),
			'shippingCountry'  => $ShippingAddr->get_country_iso2(),
			'company'		   => $BillingAddr->get('addressCompany'),
			'email'            => $Customer->customerEmail(),
		];

		$card = new CreditCard($data);

		return $card;
	}

	public function get_exchange_rate($Order)
	{
		return null;
	}
}
