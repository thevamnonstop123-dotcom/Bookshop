<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    */

    'defaults' => [
        'guard' => 'customer',
        'passwords' => 'customers',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    */

    'guards' => [
        'customer' => [
            'driver' => 'session',
            'provider' => 'customers',
        ],

        'staff' => [
            'driver' => 'session',
            'provider' => 'staff',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    */

    'providers' => [
        'customers' => [
            'driver' => 'eloquent',
            'model' => App\Models\Customer::class,
        ],

        'staff' => [
            'driver' => 'eloquent',
            'model' => App\Models\Staff::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Reset Config
    |--------------------------------------------------------------------------
    */

    'passwords' => [
         'customers' => [
            'provider' => 'customers',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],

        'staff' => [
            'provider' => 'staff',
            'table' => 'password_reset_tokens',
            'expire' => 60,
        ],
    ],

    'password_timeout' => 60,

];