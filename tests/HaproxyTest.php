<?php

namespace rethink\hrouter\tests\services;

use blink\support\Json;
use PHPUnit_Framework_TestCase;
use rethink\hrouter\Haproxy;

/**
 * Class HaproxyTest
 *
 * @package rethink\hrouter\tests\services
 */
class HaproxyTest extends PHPUnit_Framework_TestCase
{
    public function testHaproxyManagement()
    {
        $config = [
            'httpPort' => 8880,
            'httpsPort' => 4443,
        ];

        $haproxy = new Haproxy();

        $configFile = $haproxy->getConfigFile();

        file_put_contents($configFile, Json::encode($config));

        $retval = $haproxy->start($output);

        if ($retval != 0) {
            var_dump($output);
        }

        $this->assertEquals(0, $retval);

        sleep(1);

        $this->assertEquals(0, $haproxy->reload());

        sleep(1);

        $this->assertTrue($haproxy->stop());
    }
}