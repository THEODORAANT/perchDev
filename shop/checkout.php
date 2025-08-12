<? ob_start();?>
<?php include('../perch/runtime.php');?>

<?php
  perch_shop_payment_form('stripe');

if (perch_member_logged_in() && perch_post('stripeToken')) {

  // your 'return'  URL
  $return_url = 'http://localhost/perch-dev/shop/payment';
  $cancel_url = 'payment/went/wrong';

  perch_shop_checkout('stripe', [
       'return_url' => $return_url,
       'cancel_url'=> $cancel_url,
       'confirm' => true,
       'token'      => perch_post('stripeToken')
     ]);

}

/*
if (perch_member_logged_in()) {



$terms_url = 'https://yourstore.com/perch-dev/shop/payment/terms';
  $checkout_url = 'https://yourstore.com/perch-dev/payment/cancel';
$confirmation_url = 'https://localhost/perch-dev/shop/payment?klarna_order_id={checkout.order.id}';



  perch_shop_checkout('klarna', [
    'terms_url' => $terms_url,
    'checkout_url' => $checkout_url,
 'confirmation_url'=> $confirmation_url,

  ]);
}else{
  // Returning customer login form
    perch_shop_login_form();

    // New customer sign up form
    perch_shop_registration_form();
}
*/
/*
// Klarna API credentials
$merchant_id = '2dcf1465-0ca1-424f-aa72-5462cba12480';
$shared_secret = 'klarna_test_api_MkZBN0xHVSNHd0YpL1M5dCpoWG8qa1VaLyN4M3gxTlAsMmRjZjE0NjUtMGNhMS00MjRmLWFhNzItNTQ2MmNiYTEyNDgwLDEsR1NpaDlZRGdGVWs4R2ZZMyt0OGlzKzVwRVpDaTU2TzhGdW0zQnJENEF1RT0';


// Klarna API endpoint (use sandbox for testing)
//$api_url = 'https://api.playground.klarna.com/payments/v1/sessions'; // Use the production URL when live
$api_url = 'https://api.playground.klarna.com/checkout/v3/orders';
$klarna_available_countries = array(
    array("Country"=>"Australia", "purchase_country"=>"AU", "locallocale"=>"en-AU", "currency"=>"AUD"),
    array("Country"=>"Austria", "purchase_country"=>"AT", "locallocale"=>"de-AT, en-AT", "currency"=>"EUR"),
    array("Country"=>"Belgium", "purchase_country"=>"BE", "locallocale"=>"nl-BE, fr-BE, en-BE", "currency"=>"EUR"),
    array("Country"=>"Canada", "purchase_country"=>"CA", "locallocale"=>"en-CA, fr-CA", "currency"=>"CAD"),
    array("Country"=>"Czech Republic", "purchase_country"=>"CZ", "locallocale"=>"cs-CZ, en-CZ", "currency"=>"CZK"),
    array("Country"=>"Denmark", "purchase_country"=>"DK", "locallocale"=>"da-DK, en-DK", "currency"=>"DKK"),
    array("Country"=>"Finland", "purchase_country"=>"FI", "locallocale"=>"fi-FI, sv-FI, en-FI", "currency"=>"EUR"),
    array("Country"=>"France", "purchase_country"=>"FR", "locallocale"=>"fr-FR, en-FR", "currency"=>"EUR"),
    array("Country"=>"Germany", "purchase_country"=>"DE", "locallocale"=>"de-DE, en-DE", "currency"=>"EUR"),
    array("Country"=>"Greece*", "purchase_country"=>"GR", "locallocale"=>"el-GR, en-GR", "currency"=>"EUR"),
    array("Country"=>"Hungary", "purchase_country"=>"HU", "locallocale"=>"hu-HU, en-HU", "currency"=>"HUF"),
    array("Country"=>"Ireland (Republic of Ireland)", "purchase_country"=>"IE", "val2"=>"en-IE", "currency"=>"EUR"),
    array("Country"=>"Italy", "purchase_country"=>"IT", "locallocale"=>"it-IT, en-IT", "currency"=>"EUR"),
    array("Country"=>"Mexico", "purchase_country"=>"MX", "locallocale"=>"en-MX, es-MX", "currency"=>"MXN"),
    array("Country"=>"Netherlands", "purchase_country"=>"NL", "locallocale"=>"nl-NL, en-NL", "currency"=>"EUR"),
    array("Country"=>"New Zealand", "purchase_country"=>"NZ", "locallocale"=>"en-NZ", "currency"=>"NZD"),
    array("Country"=>"Norway", "purchase_country"=>"NO", "locallocale"=>"nb-NO, en-NO", "currency"=>"NOK"),
    array("Country"=>"Poland", "purchase_country"=>"PL", "locallocale"=>"pl-PL, en-PL", "currency"=>"PLN"),
    array("Country"=>"Portugal", "purchase_country"=>"PT", "locallocale"=>"pt-PT, en-PT", "currency"=>"EUR"),
    array("Country"=>"Romania", "purchase_country"=>"RO", "locallocale"=>"ro-RO, en-RO", "currency"=>"RON"),
    array("Country"=>"Slovakia", "purchase_country"=>"SK", "locallocale"=>"sk-SK, en-SK", "currency"=>"EUR"),
    array("Country"=>"Spain", "purchase_country"=>"ES", "locallocale"=>"es-ES, en-ES", "currency"=>"EUR"),
    array("Country"=>"Sweden", "purchase_country"=>"SE", "locallocale"=>"sv-SE, en-SE", "currency"=>"SEK"),
    array("Country"=>"Switzerland", "purchase_country"=>"CH", "locallocale"=>"de-CH, fr-CH, it-CH, en-CH", "currency"=>"CHF"),
    array("Country"=>"United Kingdom", "purchase_country"=>"GB", "locallocale"=>"en-GB", "currency"=>"GBP"),
    array("Country"=>"United States", "purchase_country"=>"US", "locallocale"=>"en-US, es-US", "currency"=>"USD")
);
// Order data


$orderData = [
    "purchase_country" => "US",
    "purchase_currency" => "USD",
    "locale" => "en-US",
    "order_amount" => 4830, // in cents (e.g., $50.00)
    "order_tax_amount" => 903, // in cents
    "order_lines" => [
        [
            "type" => "physical",
            "name" => "T-shirt",
            "quantity" => 1,
            "unit_price" => 4830,
            "tax_rate" => 2300,
            "total_amount" => 4830,
            "total_tax_amount" => 903
        ]
    ],
    "merchant_urls" => [
        "terms" => "https://yourstore.com/terms",
        "checkout" => "https://yourstore.com/checkout",
        "confirmation" => "https://yourstore.com/confirmation",
        "push" => "https://yourstore.com/api/push}"
    ]
];


// Convert order data to JSON
$order_json = json_encode($orderData);

// Initialize cURL
$ch = curl_init($api_url);

// Set cURL options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the response as a string
curl_setopt($ch, CURLOPT_POST, true); // Use POST method
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Basic " . base64_encode($merchant_id . ':' . $shared_secret),
    "Content-Type: application/json",
    "Accept: application/json"
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, $order_json); // Attach the order data

// Execute cURL request
echo "respone******";
$response = curl_exec($ch);

// Check for errors in cURL
if (curl_errno($ch)) {
    echo "cURL Error: " . curl_error($ch);
} else {
    // Parse the response
    $response_data = json_decode($response, true);
    print_r(  $response_data);
//$token=$response_data["client_token"];
// Initialize cURL session
/*$ch1 = curl_init();
echo 'https://api.playground.klarna.com/payments/v1/sessions/'.$response_data["session_id"];
// Set cURL options
curl_setopt($ch1, CURLOPT_URL,  'https://api.playground.klarna.com/payments/v1/sessions/'.$response_data["session_id"]);  // Replace with the actual URL
curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);  // Return the response as a string
curl_setopt($ch1, CURLOPT_HTTPHEADER, [
    "Authorization: Basic " . base64_encode($merchant_id . ':' . $shared_secret),
    "Content-Type: application/json",
    "Accept: application/json"
]);
curl_setopt($ch1, CURLOPT_HTTPGET, true);  // Indicate that it's a GET request

// Execute cURL request and store the response
$response1 = curl_exec($ch1);

// Check for errors
if($response1 === false) {
    echo "cURL Error: " . curl_error($ch1);
} else {
    echo "Response1: " . $response1;
     $response_data1 = json_decode($response1, true);
    $token=$response_data1["client_token"];
}

// Close cURL session
curl_close($ch1);
*/
    // Handle response
   /* if (isset($response_data['checkout_url'])) {
        // If order is created successfully, redirect to Klarna Checkout page
        echo "Klarna Order Created! Redirecting to Klarna Checkout: " . $response_data['checkout_url'];
        header("Location: " . $response_data['checkout_url']);
        exit();
    } else {
        // Handle errors or failed order creation
        echo "Error creating Klarna order: " . $response_data['error_messages'][0];
    }*/
    /*
}


// Close cURL session
curl_close($ch);

$order_json = json_encode($order_data);

// Initialize cURL
//$ch = curl_init('https://api.playground.klarna.com/payments/v1/authorizations/'.$token.'/order' );
$ch = curl_init('https://api.playground.klarna.com/customer-token/v1/tokens/'.$token.'/order'  );

// Set cURL options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the response as a string
curl_setopt($ch, CURLOPT_POST, true); // Use POST method
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Basic " . base64_encode($merchant_id . ':' . $shared_secret),
    "Content-Type: application/json",
    "Accept: application/json"
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, $order_json); // Attach the order data

// Execute cURL request
echo "respone";
$response = curl_exec($ch);

// Check for errors in cURL
if (curl_errno($ch)) {
    echo "cURL Error: " . curl_error($ch);
} else {
    // Parse the response
    $response_data = json_decode($response, true);
    print_r(  $response_data);

    // Handle response
   /* if (isset($response_data['checkout_url'])) {
        // If order is created successfully, redirect to Klarna Checkout page
        echo "Klarna Order Created! Redirecting to Klarna Checkout: " . $response_data['checkout_url'];
        header("Location: " . $response_data['checkout_url']);
        exit();
    } else {
        // Handle errors or failed order creation
        echo "Error creating Klarna order: " . $response_data['error_messages'][0];
    }*/
//}

// Close cURL session
//curl_close($ch);

?>

