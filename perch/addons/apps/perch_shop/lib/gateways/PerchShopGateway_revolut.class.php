<?php
class PerchShopGateway_revolut extends PerchShopGateway_default
{

	public function complete_payment($order_id, $gateway_opts=array())
	{
$details=$this->get_revolut_order_details($order_id);
//echo "get_revolut_order_details";print_r($details);
		//$this->init_cart();
		PerchUtil::debug('Runtime complete_payment for revolut');

		$Orders = new PerchShop_Orders($this->api);
		$Order  = false;
		//echo "callback_looks_valid";print_r($this->callback_looks_valid($details));


		if ($this->callback_looks_valid($details)) {
			$Order = $this->get_order_from_env($Orders,"", $details);


			if ($Order) {
			    if($Order->orderStatus()!="paid"){
				$this->Order = $Order;


				$result = $this->action_payment_callback($Order, $details, $gateway_opts);

				if ($result) {
					PerchUtil::debug('Completing order');
					return $Order->complete_payment($args, $gateway_opts);
				}else{
					return $result;
				}
				}else{
                 				return [
                 					'status' => 'error',
                 					'message' => 'Order already completed.',
                 				];
                 			}
			}else{
				return [
					'status' => 'error',
					'message' => 'Order not found.',
				];
			}
		}else{
			return [
				'status' => 'error',
				'message' => 'Invalid callback.',
			];
		}
	}
	public function handle_successful_payment($Order, $response, $gateway_opts)
	{

		$Order->finalize_as_paid();


        return true;


	}

	public function handle_failed_payment($Order, $response, $gateway_opts)
	{
		$Order->set_status('payment_failed');

		if (isset($gateway_opts['cancel_url'])) {
			PerchUtil::redirect($gateway_opts['cancel_url']);
		}
	}

	public function get_api_url($config)
	{

		if ($config['test_mode'] ) {
			return 'https://sandbox-merchant.revolut.com/api/1.0';
		}
		return 'https://merchant.revolut.com/api/1.0';
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
		public function get_revolut_order_details( $order_id)
        	{

    $config = PerchShop_Config::get('gateways', $this->slug);
		$api_key = $this->get_api_key($config);
		$api_url = $this->get_api_url($config);
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => $api_url.'/orders/'.$order_id,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'Accept: application/json',
    'Authorization: Bearer '.$api_key
  ),
));

$response = curl_exec($curl);
  $responserev=json_decode($response,true) ;
curl_close($curl);
return $responserev;

    }


	public function take_payment($Order, $opts)
	{
	//echo "take_payment revolut";echo "take_payment revolut";print_r($Order);
		$payment_opts = [
				'amount'        => strval($Order->orderTotal()*100),
				'currency'      => $Order->get_currency_code(),
				'merchant_order_ext_ref' => $Order->id(),
				'description'	=> 'Order #'.$Order->id(),
					'email'	=> 'example.customer@email.com',
					'redirectUrls'=>[
                        'success'=> 'https://www.example.com/success',
                        'failure'=> 'https://www.example.com/failure',
                        'cancel'=> 'https://www.example.com/cancel']


		    ];
        $config = PerchShop_Config::get('gateways', $this->slug);
		$opts = array_merge($opts, $payment_opts);

		$opts = $this->format_payment_options($Order, $opts);


    $config = PerchShop_Config::get('gateways', $this->slug);

		$api_key = $this->get_api_key($config);
		$api_url = $this->get_api_url($config);
		echo "api_url"; echo $api_url ;
try{

    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => $api_url.'/orders',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS =>   json_encode($opts, JSON_NUMERIC_CHECK),
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
        'Accept: application/json',
        'Authorization: Bearer '.$api_key
      ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);

   $responserev=json_decode($response,true) ;
   if(isset($responserev["id"])){
    $Order->set_transaction_reference($responserev["id"]);
echo "SCRIPT_NAME";
echo $_SERVER['SCRIPT_NAME'];
    		    // Redirect to offsite payment gateway
    		    PerchUtil::debug('Payment redirect response');
    		    PerchUtil::debug($response);
   //  PerchUtil::redirect($_SERVER['SCRIPT_NAME']."?token=".$responserev["public_id"]."&revolutid=".$responserev["id"]);
   }

}catch (Exception $e) {
 			print_r($e->getMessage());

}


 return ;
}
	/*public function get_exchange_rate($Order)
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
	}*/

	private function init_native_stripe_api()
	{
		$config = PerchShop_Config::get('gateways', $this->slug);
		$api_key = $this->get_api_key($config);

		\Stripe\Stripe::setApiKey($api_key);
	}

	public function get_order_from_env($Orders, $get, $post)
    	{
    		if (isset($post['id'])) {
    			return $Orders->get_one_by('orderGatewayRef', $post['id']);
    		}
    	}

	public function callback_looks_valid($get=array(), $post=array())
	{
		if (isset($post['id']) or isset($get['id'])) {
			return true;
		}
		return false;
	}

	public function action_payment_callback($Order, $args, $gateway_opts)
    {

    	$result = $Order->finalize_as_paid();
    }
}
