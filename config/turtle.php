<?php

return [

    // app features
    'allow' => [
        'frontend' => true,
        'registration' => true,
        'contact' => true,
        'billing' => true,
    ],

    // billing
    'billing' => [
        'stripe_secret_key' => 'sk_test_QSlbZsEyVA0pRaqyWKduKRYT',
        'trial_period' => '30 days',
        'plans' => [
            // Stripe plan ID => [options]
            'basic' => [
                'name' => 'Basic',
                'description' => 'A great starter plan for individuals.',
                'price' => '$10',
                'period' => 'month',
                'limits' => [
                    // model => limit
                    'App\Post' => 5,
                    'App\Page' => 5,
                ],
            ],
            'plus' => [
                'name' => 'Plus',
                'description' => 'A bit more flexible, ideal for teams.',
                'price' => '$20',
                'period' => 'month',
                'limits' => [
                    'App\Post' => 25,
                    'App\Page' => 25,
                ],
            ],
            'premium' => [
                'name' => 'Premium',
                'description' => 'Fully unlimited! Perfect for companies.',
                'price' => '$50',
                'period' => 'month',
                'limits' => [
                    'App\Post' => null, // null = unlimited
                    'App\Page' => null, // null = unlimited
                ],
            ],
        ],
    ],

    // demo mode
    'demo_mode' => false,

    // recaptcha keys
    'recaptcha' => [
        'site_key' => '6LdjhS8UAAAAAHHPYMOdcbIoe4WN3mu231F4f9x7',
        'secret_key' => '6LdjhS8UAAAAALu4GUV0lmIic6FR4kuILvRAMi16',
    ],

    // classes used
    'controllers' => [
        'app' => 'Kjdion84\Turtle\Controllers\AppController',
        'auth' => 'Kjdion84\Turtle\Controllers\AuthController',
        'role' => 'Kjdion84\Turtle\Controllers\RoleController',
        'user' => 'Kjdion84\Turtle\Controllers\UserController',
    ],
    'models' => [
        'activity' => 'Kjdion84\Turtle\Models\Activity',
        'billing' => 'Kjdion84\Turtle\Models\Billing',
        'permission' => 'Kjdion84\Turtle\Models\Permission',
        'role' => 'Kjdion84\Turtle\Models\Role',
        'user' => 'App\User',
    ],

];