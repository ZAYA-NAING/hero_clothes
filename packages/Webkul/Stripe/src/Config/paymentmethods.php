<?php

return [
    'stripe_smart_button' => [
        'code'                   => 'stripe_smart_button',
        'title'                  => 'Stripe Smart Button',
        'description'            => 'Stripe',
        'client_id'              => 'pk_test_51PDRKRP0eVufA6Xrx3ou3mWQEjSTyXf6lYOQe4VvIdqYTNEQYoLJB4oMIsMdeOJ5SyRwDhtbk4dZXaqPgLoV3AI700xtVlbkI4',
        'class'                  => 'Webkul\Stripe\Payment\SmartButton',
        'sandbox'                => true,
        'active'                 => true,
        'sort'                   => 10,
    ],

    'paypal_standard' => [
        'code'             => 'paypal_standard',
        'title'            => 'PayPal Standard',
        'description'      => 'PayPal Standard',
        'class'            => 'Webkul\Paypal\Payment\Standard',
        'sandbox'          => true,
        'active'           => true,
        'business_account' => 'test@webkul.com',
        'sort'             => 3,
    ],
];
