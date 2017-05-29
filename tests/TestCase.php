<?php

namespace app\tests;

use blink\testing\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{

    public function createApplication()
    {
        return require __DIR__ . '/../src/bootstrap.php';
    }

}
