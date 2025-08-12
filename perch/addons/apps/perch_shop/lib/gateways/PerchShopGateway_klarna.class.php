<?php
class PerchShopGateway_klarna extends PerchShopGateway_default
{ public $Order ;
	public function get_transaction_data($Order)
	{

		$orderData = $this->get_klarna_order_details( $Order->orderGatewayRef());

		return $orderData;
	}
public function confirm_klarna_payment($order_id ){
 $details=$this->get_klarna_order_details($order_id);
      //  echo "get_klarna_order_details";print_r($details);
        echo $details['html_snippet'];
        return;

}
	public function complete_payment($order_id, $gateway_opts=array())
	{
        $details=$this->get_klarna_order_details($order_id);
       // echo "get_klarna_order_details";print_r($details);
		//$this->init_cart();
		PerchUtil::debug('Runtime complete_payment for revolut');

		$Orders = new PerchShop_Orders($this->api);
		$Order  = false;


		if ($this->callback_looks_valid($details)) {
			$Order = $this->get_order_from_env($Orders,"", $details);


			if ($Order) {
			    if($Order->orderStatus()!="paid"){
				$this->Order = $Order;

				$result = $this->action_payment_callback($Order, $details, $gateway_opts);
//echo "order result";
		//	print_r($result );

				if ($result) {
					PerchUtil::debug('Completing order');
					//echo "order capture_klarna_order";

					$result_capture = $this->capture_klarna_order( $details["order_id"],$details["order_amount"]);
					//print_r($result_capture );
					//return $Order->complete_payment($details, $gateway_opts);
					 $this->handle_successful_payment($Order, $result_capture, $gateway_opts);
				}else{
				  $this->handle_failed_payment($Order, $gateway_opts);
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

if (isset($gateway_opts['success_url'])) {
			PerchUtil::redirect($gateway_opts['success_url']);
		}
        return true;


	}

	public function handle_failed_payment($Order, $response, $gateway_opts)
	{
		$Order->set_status('payment_failed');

		if (isset($gateway_opts['cancel_url'])) {
			PerchUtil::redirect($gateway_opts['cancel_url']);
		}
	}
	public function get_klarna_locale($purchase_country){
	$klarna_available_countries = array(
       "AU" =>array("Country"=>"Australia", "purchase_country"=>"AU", "locallocale"=>"en-AU", "currency"=>"AUD"),
       "AT"=> array("Country"=>"Austria", "purchase_country"=>"AT", "locallocale"=>"de-AT, en-AT", "currency"=>"EUR"),
       "BE"=> array("Country"=>"Belgium", "purchase_country"=>"BE", "locallocale"=>"nl-BE, fr-BE, en-BE", "currency"=>"EUR"),
       "CA"=> array("Country"=>"Canada", "purchase_country"=>"CA", "locallocale"=>"en-CA, fr-CA", "currency"=>"CAD"),
        "CZ"=>array("Country"=>"Czech Republic", "purchase_country"=>"CZ", "locallocale"=>"cs-CZ, en-CZ", "currency"=>"CZK"),
        "DK"=>array("Country"=>"Denmark", "purchase_country"=>"DK", "locallocale"=>"da-DK, en-DK", "currency"=>"DKK"),
        "FI"=>array("Country"=>"Finland", "purchase_country"=>"FI", "locallocale"=>"fi-FI, sv-FI, en-FI", "currency"=>"EUR"),
        "FR"=>array("Country"=>"France", "purchase_country"=>"FR", "locallocale"=>"fr-FR, en-FR", "currency"=>"EUR"),
        "DE"=>array("Country"=>"Germany", "purchase_country"=>"DE", "locallocale"=>"de-DE, en-DE", "currency"=>"EUR"),
        "GR"=>array("Country"=>"Greece*", "purchase_country"=>"GR", "locallocale"=>"el-GR, en-GR", "currency"=>"EUR"),
        "HU"=>array("Country"=>"Hungary", "purchase_country"=>"HU", "locallocale"=>"hu-HU, en-HU", "currency"=>"HUF"),
        "IE"=>array("Country"=>"Ireland (Republic of Ireland)", "purchase_country"=>"IE", "val2"=>"en-IE", "currency"=>"EUR"),
        "IT"=>array("Country"=>"Italy", "purchase_country"=>"IT", "locallocale"=>"it-IT, en-IT", "currency"=>"EUR"),
        "MX"=>array("Country"=>"Mexico", "purchase_country"=>"MX", "locallocale"=>"en-MX, es-MX", "currency"=>"MXN"),
        "NL"=>array("Country"=>"Netherlands", "purchase_country"=>"NL", "locallocale"=>"nl-NL, en-NL", "currency"=>"EUR"),
        "NZ"=>array("Country"=>"New Zealand", "purchase_country"=>"NZ", "locallocale"=>"en-NZ", "currency"=>"NZD"),
        "NO"=>array("Country"=>"Norway", "purchase_country"=>"NO", "locallocale"=>"nb-NO, en-NO", "currency"=>"NOK"),
        "PL"=>array("Country"=>"Poland", "purchase_country"=>"PL", "locallocale"=>"pl-PL, en-PL", "currency"=>"PLN"),
        "PT"=>array("Country"=>"Portugal", "purchase_country"=>"PT", "locallocale"=>"pt-PT, en-PT", "currency"=>"EUR"),
        "RO"=>array("Country"=>"Romania", "purchase_country"=>"RO", "locallocale"=>"ro-RO, en-RO", "currency"=>"RON"),
        "SK"=>array("Country"=>"Slovakia", "purchase_country"=>"SK", "locallocale"=>"sk-SK, en-SK", "currency"=>"EUR"),
        "ES"=>array("Country"=>"Spain", "purchase_country"=>"ES", "locallocale"=>"es-ES, en-ES", "currency"=>"EUR"),
        "SE"=>array("Country"=>"Sweden", "purchase_country"=>"SE", "locallocale"=>"sv-SE, en-SE", "currency"=>"SEK"),
        "CH"=>array("Country"=>"Switzerland", "purchase_country"=>"CH", "locallocale"=>"de-CH, fr-CH, it-CH, en-CH", "currency"=>"CHF"),
        "GB"=>array("Country"=>"United Kingdom", "purchase_country"=>"GB", "locallocale"=>"en-GB", "currency"=>"GBP"),
        "US"=>array("Country"=>"United States", "purchase_country"=>"US", "locallocale"=>"en-US, es-US", "currency"=>"USD")
    );

return $klarna_available_countries[$purchase_country];
    }

	public function get_api_url($config)
	{

		if ($config['test_mode'] ) {
			return  'https://api.playground.klarna.com';
		}
		return 'https://api.klarna.com';
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

	public function get_merchantId($config)
    	{
    		if ($config['test_mode']) {
    			return $config['test']['merchantId'];
    		}
    		return $config['live']['merchantId'];
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

	function generate_uuid_v4() {
        // Generate 16 random bytes (128 bits)
        $data = random_bytes(16);

        // Set version to 4 (UUIDv4)
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // Set the version to 4
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // Set the variant to RFC4122

        // Format the data as a UUID string
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }


		public function capture_klarna_order( $orderId,$captured_amount)
      {

    $config = PerchShop_Config::get('gateways', $this->slug);
		$api_key = $this->get_api_key($config);

		$sharedSecret = $this->get_api_key($config);
		$api_url = $this->get_api_url($config);
		$merchantId= $this->get_public_api_key($config);
 $baseUrl = $config['test_mode'] ? "https://api.playground.klarna.com" : "https://api.klarna.com";
    $url = $baseUrl . "/ordermanagement/v1/orders/" . $orderId."/captures";
    $Ikey=$this->generate_uuid_v4();

    $curl = curl_init();
$payment_opts = [
    "captured_amount" =>  $captured_amount
    ];

    curl_setopt_array($curl, array(
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS =>   json_encode($payment_opts, JSON_NUMERIC_CHECK),
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
        'Accept: application/json',
        'Klarna-Idempotency-Key:'.$Ikey,
        'Authorization: Basic ' . base64_encode($sharedSecret . ':' . $merchantId)
      ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);

  $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
// echo "responswe2222";


 //   print_r( $response);
 //   print_r( $httpCode);
 if ($httpCode === 200) {
      $orderDetails=json_decode($response, true);
      // file_put_contents("logs.txt", "Push received: " . json_encode($orderDetails), FILE_APPEND);
    return $orderDetails;
    }
    return false;
      }
		public function get_klarna_order_details( $orderId)
        	{

    $config = PerchShop_Config::get('gateways', $this->slug);
		$api_key = $this->get_api_key($config);

		$sharedSecret = $this->get_api_key($config);
		$api_url = $this->get_api_url($config);
		$merchantId= $this->get_public_api_key($config);
    $url = $api_url . "/checkout/v3/orders/" . $orderId;

try{


    // Initialize cURL
    $ch = curl_init($url);

    // Set cURL options
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Basic ' . base64_encode($sharedSecret . ':' . $merchantId)
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Execute cURL request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
 //echo "responswe1111";


    //print_r( $response);
   // print_r( $httpCode);
    // Close cURL
    curl_close($ch);
}catch (Exception $e) {
 			print_r($e->getMessage());

}
    // Return response
      if ($httpCode === 200) {
      $orderDetails=json_decode($response, true);
      // file_put_contents("logs.txt", "Push received: " . json_encode($orderDetails), FILE_APPEND);
    return $orderDetails;
    }
    return false;



    }


	public function take_payment($Order, $opts)
	{
//echo "take_payment klarna";
//print_r($Order);echo "***";

           $order_details=$Order->get_for_template();

    //  print_r( $order_details);
      $items = [];
foreach($order_details['items'] as $item) {
$klarnaitem = [];
$klarnaitem["type"] ="physical";
$klarnaitem["name"] = $item["title"];
$klarnaitem["quantity"] = 1;
$klarnaitem["unit_price"] = strval( $item["total_with_tax"]*100);
$klarnaitem["tax_rate"] =strval( $item["tax_rate"]*100);
$klarnaitem["total_amount"] = strval( $item["total_with_tax"]*100);
$klarnaitem["total_tax_amount"] = strval( $item["total_tax"]*100);
 $items[] = $klarnaitem;
}
 $Customers = new PerchShop_Customers($this->api);
        $Customer = $Customers->find($order_details["customerID"]);

                  $Addresses = new PerchShop_Addresses($this->api);

            $BillingAddr  = $Addresses->find((int)$Order->orderBillingAddress());

          $locale=$this->get_klarna_locale($BillingAddr->get_country_iso2());


$host = $_SERVER['HTTP_HOST'];  // or $_SERVER['SERVER_NAME']
// "push": "https://merchant.com/kco/push.php?klarna_order_id={checkout.order.id}"
// Check if the host contains 'http' or 'https'
if (strpos($host, 'http://') === 0 or strpos($host, 'https://') === 0) {
    $push_url=$host."/perch/addons/apps/perch_shop/webhook/klarna.php?klarna_order_id={checkout.order.id}";
} else {
   $push_url = 'https://nlclinic-dev.azurewebsites.net/payment?klarna_order_id={checkout.order.id}';
}

$payment_opts = [
    "purchase_country" =>  $BillingAddr->get_country_iso2(),
    "purchase_currency" => $Order->get_currency_code(),
    "locale" => $locale["locallocale"],
    "order_amount" =>  strval( $order_details["orderTotal"]*100), // in cents (e.g., $50.00)
   "order_tax_amount" => strval( $order_details["total_tax"]*100), // in cents
    "order_lines" =>  $items,
    "merchant_urls" => [
        "terms" =>  $opts["terms_url"],
        "checkout" => $opts["checkout_url"],
        "confirmation" => $opts["confirmation_url"],
        "push" => $push_url
    ]
];

        $config = PerchShop_Config::get('gateways', $this->slug);
		$opts = array_merge($opts, $payment_opts);

		$opts = $this->format_payment_options($Order, $opts);


    $config = PerchShop_Config::get('gateways', $this->slug);

		$sharedSecret = $this->get_api_key($config);
		$api_url = $this->get_api_url($config);
		$merchantId= $this->get_public_api_key($config);



try{

    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => $api_url.'/checkout/v3/orders',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS =>   json_encode($payment_opts, JSON_NUMERIC_CHECK),
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
        'Accept: application/json',
         'Authorization: Basic ' . base64_encode($sharedSecret . ':' . $merchantId)
      ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
//echo "response";
//print_r($response);

   if ($response) {
       $responseData = json_decode($response, true);
       $Order->set_transaction_reference($responseData["order_id"]);
       if (isset($responseData['html_snippet'])) {
           // Redirect the user to Klarna's Checkout page
          echo $responseData['html_snippet'];
           exit;
       } else {
           echo "Error creating Klarna Checkout order: " . $responseData['error']['message'];
       }
   } else {
       echo "API request failed.";
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
    		if (isset($post['order_id'])) {
    			return $Orders->get_one_by('orderGatewayRef', $post['order_id']);
    		}
    	}

	public function callback_looks_valid($get=array(), $post=array())
	{
		if (isset($post['order_id']) or isset($get['order_id'])) {
			return true;
		}
		return false;
	}

	public function action_payment_callback($Order, $args, $gateway_opts)
    {

    	$result = $Order->finalize_as_paid();
    		if ($Order->orderStatus()=="paid") {
        			return true;
        		}
        		return false;
    }
}
