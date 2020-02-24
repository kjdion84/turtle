<?php

return [

    // app features
    'allow' => [
        'frontend' => true,
        'registration' => true,
        'contact' => true,
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
        'permission' => 'Kjdion84\Turtle\Models\Permission',
        'role' => 'Kjdion84\Turtle\Models\Role',
        'user' => 'App\User',
    ],
    'routes' => [
        'prefix' => 'dashboard'
    ]
];