<?php
return [
    'request' => [
        'class' => \blink\http\Request::class,
        'middleware' => [
            \rethink\hrouter\http\middleware\BasicAuth::class,
        ],
    ],
    'response' => [
        'class' => \blink\http\Response::class,
        'middleware' => [
            \rethink\hrouter\http\middleware\ResponseFormatter::class,
        ],
    ],
    'capsule' => [
        'class' => blink\laravel\database\Manager::class,
        'fetch' => PDO::FETCH_CLASS,
        'default' => BLINK_ENV === 'test' ? 'sqlite.test' : 'sqlite.prod',
        'connections' => [
            'sqlite.prod' => [
                'driver'   => 'sqlite',
                'database' => __DIR__.'/../../runtime/prod.sqlite',
                'prefix'   => '',
            ],
            'sqlite.test' => [
                'driver'   => 'sqlite',
                'database' => __DIR__.'/../../runtime/test.sqlite',
                'prefix'   => '',
            ],
        ],
    ],
    'services' => [
        'class' => \rethink\hrouter\services\Services::class,
    ],
    'nodes' => [
        'class' => \rethink\hrouter\services\Nodes::class,
    ],
    'routes' => [
        'class' => \rethink\hrouter\services\Routes::class,
    ],
    'domains' => [
        'class' => \rethink\hrouter\services\Domains::class,
    ],
    'settings' => [
        'class' => \rethink\hrouter\services\Settings::class,
    ],
    'haproxy' => [
        'class' => \rethink\hrouter\Haproxy::class,
        'executable' => env('haproxy_executable', 'haproxy'),
        'configDir' => env('haproxy_config_dir', '/etc/haproxy'),
        'supervised' => env('haproxy_supervised', 1),
        'username' => env('username', 'admin'),
        'password' => env('password', 'haproxy-router'),
        'commands' => [
            'start' => env('haproxy_exec_start', 'service haproxy start'),
            'stop' => env('haproxy_exec_stop', 'service haproxy stop'),
            'reload' => env('haproxy_exec_reload', 'service haproxy reload'),
        ],
    ],
    'challenges' => [
        'class'=> \rethink\hrouter\services\Challenges::class,
    ],
    'acme' => [
        'class' => \rethink\hrouter\services\Acme::class,
        'email' => env('acme_email', 'haproxy-router@rethinkphp.com'),
        'directoryUrl' => env('acme_directory_url', 'https://acme-v01.api.letsencrypt.org/directory'),
    ],
    'i18n' => [
        'class' => blink\i18n\Translator::class,
        'loaders' => [
            'php' => Symfony\Component\Translation\Loader\PhpFileLoader::class,
        ],
        'resources' => [
            [
                'format' => 'php',
                'resource' => __DIR__ . '/../i18n/en-US/messages.php',
                'locale' => 'en-US'
            ],
            [
                'format' => 'php',
                'resource' => __DIR__ . '/../i18n/zh-CN/messages.php',
                'locale' => 'zh-CN'
            ]
        ],
    ],
    'log' => [
        'class' => 'blink\log\Logger',
        'targets' => [
            'file' => [
                'class' => 'blink\log\StreamTarget',
                'enabled' => BLINK_ENV != 'test',
                'stream' => env('logfile', 'php://stderr'),
                'level' => 'info',
            ]
        ],
    ],
];
