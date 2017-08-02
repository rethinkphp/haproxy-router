<?php
return [
    'class' => '\blink\server\SwServer',
    'name' => 'haproxy-router',
    'bootstrap' => __DIR__ . '/../bootstrap.php',
    'host' => env('listen_host', '127.0.0.1'),
    'port' => env('listen_port', 9812),
];
