<?php
return [
    'request' => [
        'class' => \blink\http\Request::class,
        'middleware' => [
            \rethink\hrouter\restapi\middleware\BasicAuth::class,
        ],
    ],
    'response' => [
        'class' => \blink\http\Response::class,
        'middleware' => [
            \rethink\hrouter\restapi\middleware\ResponseFormatter::class,
        ],
    ],
    'session' => [
        'class' => 'blink\session\Manager',
        'expires' => 3600 * 24 * 15,
        'storage' => [
            'class' => 'blink\session\FileStorage',
            'path' => __DIR__ . '/../../runtime/sessions'
        ]
    ],
    'auth' => [
        'class' => 'blink\auth\Auth',
        'model' => 'app\models\User',
    ],
    'haproxy' => [
        'class' => \rethink\hrouter\Haproxy::class,
    ],
    'log' => [
        'class' => 'blink\log\Logger',
        'targets' => [
            'file' => [
                'class' => 'blink\log\StreamTarget',
                'enabled' => BLINK_ENV != 'test',
                'stream' => 'php://stderr',
                'level' => 'info',
            ]
        ],
    ],
];
