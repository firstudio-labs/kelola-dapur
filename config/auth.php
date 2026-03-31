<?php

return [

    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
    ],

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],

    ],

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,

    'super_admin' => [
        'username' => env('SUPER_ADMIN_USERNAME', 'AnomID'),
        'password' => env('SUPER_ADMIN_PASSWORD', 'Bosidrad123'),
        'enabled' => env('SUPER_ADMIN_ENABLED', true),
        'bypass_captcha' => env('SUPER_ADMIN_BYPASS_CAPTCHA', true),
        'bypass_rate_limit' => env('SUPER_ADMIN_BYPASS_RATE_LIMIT', true),
    ]
];
