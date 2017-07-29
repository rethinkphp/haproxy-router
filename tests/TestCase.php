<?php

namespace rethink\hrouter\tests;

use blink\testing\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{

    public function setUp()
    {
        parent::setUp();

        system('echo "" > runtime/test.sqlite');
        system('php router migrate > /dev/null');
    }

    public function createApplication()
    {
        $app = require __DIR__ . '/../src/bootstrap.php';

        $runtimeDir = $app->runtime . '/tests';

        if (!file_exists($runtimeDir)) {
            mkdir($runtimeDir);
        }

        $app->services = array_merge_recursive(
            require __DIR__ . '/../src/config/services.php',
            [
                'haproxy' => [
                    'configDir' => $runtimeDir,
                ],
            ]
        );

        return $app;
    }

}
