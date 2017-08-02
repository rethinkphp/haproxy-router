<?php
return [
    'class' => '\blink\server\SwServer',
    'name' => 'haproxy-router',
    'bootstrap' => __DIR__ . '/../bootstrap.php',
    'host' => '0.0.0.0',
    'port' => 9812,
];
