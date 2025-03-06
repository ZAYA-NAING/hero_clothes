<?php

return [
    // 'stripe_standard' => [
    //     'code'             => 'stripe_standard',
    //     'title'            => 'Stripe Standard',
    //     'description'      => 'Stripe Standard',
    //     'class'            => 'Webkul\Stripe\Payment\Standard',
    //     'sandbox'          => true,
    //     'active'           => true,
    //     'business_account' => 'test@webkul.com',
    //     'sort'             => 3,
    // ],

    'stripe_smart_button' => [
        'code'        => 'stripe_smart_button',
        'title'       => 'Stripe Smart Button',
        'description' => 'Stripe',
        'client_id'   => 'sk_test_51PDRKRP0eVufA6XrxthuVY7BoAHg4rXTyGKVxSO0QzUSljV65z2WFkVAxsxrUlFSieEWFHsXzr2Ut4BBIobGaiPi00JXSRj3qd',
        'publish_key' => 'pk_test_51PDRKRP0eVufA6Xrx3ou3mWQEjSTyXf6lYOQe4VvIdqYTNEQYoLJB4oMIsMdeOJ5SyRwDhtbk4dZXaqPgLoV3AI700xtVlbkI4',
        'class'       => 'Webkul\Stripe\Payment\SmartButton',
        'sandbox'     => true,
        'active'      => true,
        'sort'        => 10,
    ],

    'stripe_checkout_session' => [
        'code'        => 'stripe_checkout_session',
        'title'       => 'Stripe',
        'description' => 'Powered by stripe',
        'client_id'   => 'sk_test_51PDRKRP0eVufA6XrxthuVY7BoAHg4rXTyGKVxSO0QzUSljV65z2WFkVAxsxrUlFSieEWFHsXzr2Ut4BBIobGaiPi00JXSRj3qd',
        'publish_key' => 'pk_test_51PDRKRP0eVufA6Xrx3ou3mWQEjSTyXf6lYOQe4VvIdqYTNEQYoLJB4oMIsMdeOJ5SyRwDhtbk4dZXaqPgLoV3AI700xtVlbkI4',
        'class'       => 'Webkul\Stripe\Payment\CheckoutSession',
        'sandbox'     => true,
        'active'      => true,
        'sort'        => 11,
    ],
];
