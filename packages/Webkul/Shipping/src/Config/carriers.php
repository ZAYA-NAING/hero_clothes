<?php

return [
    'flatrate' => [
        'code'         => 'flatrate',
        'title'        => 'Flat Rate',
        'description'  => 'Flat Rate Shipping',
        'active'       => true,
        'default_rate' => '10',
        'type'         => 'per_unit',
        'class'        => 'Webkul\Shipping\Carriers\FlatRate',
    ],

    'locationrate' => [
        'code'         => 'locationrate',
        'title'        => 'Location Rate',
        'description'  => 'Location Rate Shipping',
        'active'       => true,
        'country_active' => true,
        'default_rate' => '1',
        'type'         => 'D2D (Door to Door)',
        'class'        => 'Webkul\Shipping\Carriers\LocationRate',
    ],

    'free' => [
        'code'         => 'free',
        'title'        => 'Free Shipping',
        'description'  => 'Free Shipping',
        'active'       => true,
        'default_rate' => '0',
        'class'        => 'Webkul\Shipping\Carriers\Free',
    ],
];
