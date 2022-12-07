<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Middleware list for web routes
    |--------------------------------------------------------------------------
    |
    | You can pass any middleware for routes, by default it's just [web] group
    | of middleware.
    |
    */
    'middlewares' => [
        'api',
        'auth',
        'can:staff',
    ],

    /*
    |--------------------------------------------------------------------------
    | Route prefix
    |--------------------------------------------------------------------------
    |
    | Prefix for gui routes. By default url is [/~artisan-gui].
    | For your wish you can set it for example 'my-'. So url will be [/my-artisan-gui].
    |
    | Why tilda? It's selected for prevent route names correlation.
    |
    */
    'prefix' => 'admin/',

    /*
    |--------------------------------------------------------------------------
    | Home url
    |--------------------------------------------------------------------------
    |
    | Where to go when [home] button is pressed
    |
    */
    'home' => '/admin',

    /*
    |--------------------------------------------------------------------------
    | Only on local
    |--------------------------------------------------------------------------
    |
    | Flag that preventing showing commands if environment is on production
    |
    */
    'local' => false,


    'force-https' => env('FORCE_HTTPS_ARTISAN_GUI', true),

    /*
    |--------------------------------------------------------------------------
    | List of command permissions
    |--------------------------------------------------------------------------
    |
    | Specify permissions to every single command. Can be a string or array
    | of permissions
    |
    | Example:
    |   'make:controller' => 'create-controller',
    |   'make:event' => ['generate-files', 'create-event'],
    |
    */
    'permissions' => [
        'auth:ban-user' => 'staff',
        'auth:unban-user' => 'staff',
        'auth:get-user' => 'staff',
        'balance:value' => 'moderator',
        'balances:generate-missing' => 'developer',
        'user-balances:generate' => 'developer',
        'transfer' => 'admin',
        'address:create' => 'developer',
        'address:export' => 'admin',
        'blockchain:handle-withdraw-transaction' => 'developer',
        'blockchain:cancel-withdraw-transaction' => 'developer',
        'common:get-internal-statistic' => 'admin',
        'rates:update' => 'developer',
        'cache:clear' => 'developer',
    ],

    /*
    |--------------------------------------------------------------------------
    | List of commands
    |--------------------------------------------------------------------------
    |
    | List of all default commands that has end of execution. Commands like
    | [serve] not supported in case of server side behavior of php.
    | Keys means group. You can shuffle commands as you wish and add your own.
    |
    */
    'commands' => [
        'auth' => [
            'auth:ban-user',
            'auth:unban-user',
            'auth:get-user',
        ],
        'balances' => [
            'balance:value',
            'balances:generate-missing',
            'user-balances:generate',
        ],
        'blockchain' => [
            'transfer',
            'address:create',
            'address:export',
            'blockchain:handle-withdraw-transaction',
            'blockchain:cancel-withdraw-transaction',
        ],
        'statistic' => [
            'common:get-internal-statistic',
        ],
        'maintenance' => [
            'rates:update',
            'cache:clear',
        ],
    ]
];
