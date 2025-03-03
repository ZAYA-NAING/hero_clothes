<?php

return [
    'paypal_smart_button' => [
        'code'             => 'paypal_smart_button',
        'title'            => 'PayPal Smart Button',
        'description'      => 'PayPal',
        'client_id'        => 'AazfPuiv16oPndg6XkDil0QINBxZH4zVgn-a1himBRlESaWQzIVqavBpEZMNs3le3UE9fFdRE9e-ex3v',
        'class'            => 'Webkul\Paypal\Payment\SmartButton',
        'sandbox'          => true,
        'active'           => true,
        'sort'             => 0,
    ],

    'paypal_adv_smart_button' => [
        'code'             => 'paypal_adv_smart_button',
        'title'            => 'PayPal Advanced Smart Button',
        'description'      => 'PayPal',
        'client_id'        => 'AZpKH9atea0ib-NVm5ixh8RXdhHKVsW6r5pa4eHNOJ1P8OcWLolKm3l6i2pGjSkGdM1fSmfThLvktfBb',
        'class'            => 'Webkul\Paypal\Payment\AdvSmartButton',
        'sandbox'          => true,
        'active'           => true,
        'sort'             => 7,
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
        'types'            => ['card', 'wallet'],
    ],
];
