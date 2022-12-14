<?php

declare(strict_types=1);

use Monolog\Handler\FilterHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
    */

    'default' => env('LOG_CHANNEL', 'stack'),

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog",
    |                    "custom", "stack"
    |
    */

    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => [
                'stdout',
                'stderr',
                'sentry',
                'daily',
            ],
        ],
        'stdout' => [
            'driver' => 'monolog',
            'handler' => FilterHandler::class,
            'handler_with' => [
                'handler' => new StreamHandler('php://stdout'),
                'minLevelOrList' => Logger::DEBUG,
                'maxLevel' => Logger::NOTICE,
            ],
        ],
        'stderr' => [
            'driver' => 'monolog',
            'handler' => FilterHandler::class,
            'handler_with' => [
                'handler' => new StreamHandler('php://stderr'),
                'minLevelOrList' => Logger::WARNING,
                'maxLevel' => Logger::EMERGENCY,
            ],
        ],
        'sentry' => [
            'driver' => 'sentry',
            'level'  => 'error',
            'bubble' => true,
        ],
        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => 'debug',
            'days' => 14,
        ],
    ],
];
