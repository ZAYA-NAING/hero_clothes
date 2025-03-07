<?php

return [
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
