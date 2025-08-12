<?php
	return [

		/*
		|--------------------------------------------------------------------------
		| Gateway settings
		|--------------------------------------------------------------------------
		*/

		'gateways' => [

			'default' => [
				'enabled'   => true,
				'test_mode' => false,
				'live' => [
					'api_key'      => 'abc123',
				],
				'test' => [
					'api_key'      => 'abc123',
				],
			],

 'worldpay' => [
        'enabled'   => true,
        'test_mode' => true,
        'live' => [
            'installationId'   => '1234',
            'accountId'        => 'MYACCOUNTNAME',
            'secretWord'       => 'md5secret',
            'callbackPassword' => 'paymentresp_pws',
        ],
        'test' => [
            'installationId'   => '1234',
            'accountId'        => 'MYACCOUNTNAME',
            'secretWord'       => 'md5secret',
            'callbackPassword' => 'paymentresp_pws',
        ],
      ],
      	'revolut' => [
      				'enabled'   => true,
                  'test_mode' => true,
                      'live' => [
                        'secret_key'      => '',
                        'publishable_key' => '',
                      ],
                      'test' => [
                        'secret_key'      => 'sk_CBCSpPdEc4JUsKisaYSzcD7JPY7T4yh6sQ5vuotDp233e235LOXGhWD4qvsPAQ-g',
                        'publishable_key' => 'pk_VTdMkPG2aZ0ZkmkOQH5tAXvPKwmZZLKb5F8RlzY6D0iNwkVH',
                      ],
      			],
      			 'paypal' => [
                                        'enabled'   => true,
                                        'test_mode' => true,
                                        'live' => [
                                          'client_id'  => 'paypal_api_username',
                                          'client_secret'  => 'paypal_api_password',

                                        ],
                                        'test' => [
                                          'client_id'  => 'AVO1z6xmHDNyVNq3ZXH021xIU7bY2z7vfvxRa-7UPztuvrw0_fuz6VM42f6krObj1Maz3VvORET3-17s',
                                          'client_secret'  => 'EPcQEIxcCiwLYQ1tb0NBwql7zqhxYbz_SUkMNSGQ_ofWz1tWpuuc7Pv8uqXevM_MPSmpfHrCaBfUh2ZA',

                                        ],
                                      ],
      			 'paypal-express' => [
                        'enabled'   => true,
                        'test_mode' => true,
                        'live' => [
                          'username'  => 'paypal_api_username',
                          'password'  => 'paypal_api_password',
                          'signature' => 'perchrunway',
                        ],
                        'test' => [
                          'username'  => 'sb-tbcvg536387_api1.business.example.com',
                          'password'  => 'BZLPGTFH2ZABMPDE',
                          'signature' => 'AelMFp7dSwFvVXvMR-8tlARupRAlAzm7Os8xsTDVXhdeVMlhMkNDYCLl',
                        ],
                      ],
         	'stripeV1' => [
         				'enabled'   => true,
                     'test_mode' => true,
                         'live' => [   'secret_key'      => '',
                                                        'publishable_key' => '',
                                                      ],
                                                      'test' => [
                                                          'secret_key'      => 'sk_test_51RNsBiCeux1vWiSRwtRv3b4a2YhXSHN6z87DlVCfQYXc0HG2RfiGJPR6adKsQSVE5qYaKC5PPpngco0M34G9HXBv00nlEP6znR',
                                                                           'publishable_key' => 'pk_test_51RNsBiCeux1vWiSRg9dP1KykB31YwDWNIFEnYQTjontB9aAN7nkKucqALaFbieYrF1S7wcF4ZG0UZEzuxBUZXbOz00K3SmQEcR',



                                                      ],
                                      			],

	'stripe' => [
				'enabled'   => true,
            'test_mode' => true,
                'live' => [
                  'secret_key'      => 'sk_live_ABC123',
                  'publishable_key' => 'pk_live_51IEEhWBmfkIT8adXwz4PNKlTsXV8Hx715RmfIP9bJ4HI0GdwhxbNkfByMVPQNESImCZUMZLxQwRk0aZrk4Evu1mC00ZktdmZAw',
                ],
                'test' => [
                    'secret_key'      => 'sk_test_51QjjCWQcy4XXex9u8UqSwyNBpOZY4KAuh1NoyUSBfkttHGVLa7tZc2CdvLi6v3SNQTSgKyJ8NBuFtNw3VmT4udkT00AVUwrEwR',

                  'publishable_key' => 'pk_test_51QjjCWQcy4XXex9uMigyzIqd5ipuhxTRcltJQK1HdR1K1xg8HnJw3d5DgIvgWucKpajR1SdGIbF5xI1nJPh1M7Wm00dcCJnDWm',


                //  'secret_key'      => 'sk_test_51IAvrUCXZLrznbwDDZYBsWsZ7I7sG3XchkOkZ0FRpntS07Nb3VxXRnsPj0Id9oaaEgLJZm8CMxvk2JwhI5U7ASuL00aPppUZpr',
                // 'publishable_key' => 'pk_test_51IAvrUCXZLrznbwDGxGjCnG0HATEDRXx4cd1u8t9HLqa2iZrnE8igPlN3093dHvUzGATAua34lRZuG1IBnB7QYX500Rx7x6wvq',
                ],
			],
	'klarna' => [
				'enabled'   => true,
            'test_mode' => true,

                'live' => [
                  'secret_key'      => '',
                  'publishable_key' => '',
    'merchantId' =>  'PK380613',

                ],
                'test' => [
                //klarna_test_client_KSo3LTcjVyFpcW9MZ1Y2JE9ldEdsTFpFZkMpdT9ydVMsZTJjMmFhNDMtMjE4ZS00MTc2LTk1MDgtYWQyZWEzMWFlODRiLDEsdUNkeEtWbmd3VGZZZm1TSjVVUmxDWm8rSnprQnk5MWxXNEFHMVIwa2VPaz0
                  'secret_key'      => '2dcf1465-0ca1-424f-aa72-5462cba12480',
                  'publishable_key' => 'klarna_test_api_MkZBN0xHVSNHd0YpL1M5dCpoWG8qa1VaLyN4M3gxTlAsMmRjZjE0NjUtMGNhMS00MjRmLWFhNzItNTQ2MmNiYTEyNDgwLDEsR1NpaDlZRGdGVWs4R2ZZMyt0OGlzKzVwRVpDaTU2TzhGdW0zQnJENEF1RT0',
    'merchantId' =>  'PK380613',
                ],
			],
		],

	];
