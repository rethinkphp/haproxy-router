<?php

namespace rethink\hrouter\tests;

use PHPUnit_Framework_TestCase;
use rethink\hrouter\CfgApi;

/**
 * Class CfgApiTest
 *
 * @package rethink\hrouter\tests
 */
class CfgApiTest extends PHPUnit_Framework_TestCase
{

    public function testCreateService()
    {
        $api = new CfgApi();

        $api->createService('foo', 'foo.rethinkphp.com', []);
        $api->createService('bar', 'bar.rethinkphp.com', []);

        $this->assertEquals([
            [
                'name' => 'foo',
                'host' => 'foo.rethinkphp.com',
            ],
            [
                'name' => 'bar',
                'host' => 'bar.rethinkphp.com',
            ]
        ], $api->findServices());
    }

    /**
     * @expectedException \rethink\hrouter\support\ValidationException
     */
    public function testCreateDuplicatedService()
    {
        $api = new CfgApi();

        $api->createService('foo', 'foo.rethinkphp.com', []);
        $api->createService('foo', 'bar.rethinkphp.com', []);
    }

    public function testUpdateService()
    {
        $api = new CfgApi();

        $api->createService('foo', 'foo.rethinkphp.com', []);
        $api->createService('bar', 'bar.rethinkphp.com', []);

        $api->updateService('foo', ['host' => 'foo2.rethinkphp.com']);

        $this->assertEquals([
            [
                'name' => 'foo',
                'host' => 'foo2.rethinkphp.com',
            ],
            [
                'name' => 'bar',
                'host' => 'bar.rethinkphp.com',
            ]
        ], $api->findServices());
    }
}