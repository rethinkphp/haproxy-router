<?php

defined('BLINK_ENV') or define('BLINK_ENV', getenv('BLINK_ENV') ?: 'dev');

/**
 * Basic application configurations.
 */
$config = require __DIR__ . '/config/app.php';

/**
 * Loading routes definitions.
 */
$config['routes'] = __DIR__ . '/http/routes.php';

/**
 * Loading application service definitions.
 */
$config['services'] = __DIR__ . '/config/services.php';

/**
 * Loading application plugins definitions.
 */
$config['plugins'] = __DIR__ . '/config/plugins.php';

$app = new blink\core\Application($config);


return $app;
