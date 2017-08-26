<?php
return [
    'class' => \rethink\hrouter\Server::class,
    'name' => 'haproxy-router',
    'bootstrap' => __DIR__ . '/../bootstrap.php',
    'host' => env('listen_host', '127.0.0.1'),
    'port' => env('listen_port', 9812),
    'numWorkers' => 1,
    'numTaskers' => 1,
];
