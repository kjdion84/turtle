<?php

return [

    // app features
    'allow' => [
        'frontend' => true,
        'registration' => true,
        'contact' => true,
        'billing' => true,
    ],

    // demo mode
    'demo_mode' => false,

    // recaptcha keys
    'recaptcha' => [
        'site_key' => '6LdjhS8UAAAAAHHPYMOdcbIoe4WN3mu231F4f9x7',
        'secret_key' => '6LdjhS8UAAAAALu4GUV0lmIic6FR4kuILvRAMi16',
    ],

    // billing
    'billing' => [
        'stripe_secret_key' => 'sk_test_QSlbZsEyVA0pRaqyWKduKRYT',
        'trial' => [
            'period' => '30 days',
            'limits' => [
                // model => limit (null = unlimited)
                'App\Post' => 1,
            ],
        ],
        'plans' => [
            // Stripe plan ID => [options]
            'Basic' => [
                'html' => '
                    <p>A great starter plan for individuals.</p>
                    <ul class="list-unstyled">
                        <li><b>5</b> Posts</li>
                    </ul>
                    <p class="lead"><b>$10</b>/month</p>
                ',
                'limits' => [
                    'App\Post' => 5,
                ],
            ],
            'Plus' => [
                'html' => '
                    <p>A bit more flexible, ideal for teams.</p>
                    <ul class="list-unstyled">
                        <li><b>25</b> Posts</li>
                    </ul>
                    <p class="lead"><b>$20</b>/month</p>
                ',
                'limits' => [
                    'App\Post' => 25,
                ],
            ],
            'Premium' => [
                'html' => '
                    <p>Fully unlimited! Perfect for companies.</p>
                    <ul class="list-unstyled">
                        <li><b>Unlimited</b> Posts</li>
                    </ul>
                    <p class="lead"><b>$50</b>/month</p>
                ',
                'limits' => [
                    'App\Post' => null,
                ],
            ],
        ],
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