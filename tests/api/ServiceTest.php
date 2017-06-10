<?php

namespace rethink\hrouter\tests\api;

use rethink\hrouter\tests\TestCase;

/**
 * Class ServiceTest
 *
 * @package rethink\hrouter\tests\api
 */
class ServiceTest extends TestCase
{
    use ScenarioTrait;

    public function setUp()
    {
        parent::setUp();

        @unlink(app()->runtime . '/config.json');
    }

    public function scenarios()
    {
        return [
            // Create a service named foo
            [
                'method' => 'post',
                'path' => '/services',
                'body' => [
                    'name' => 'foo',
                    'host' => 'foo.rethinkphp.com',
                ],
                'expectedStatus' => 201,
            ],

            // Create a service named bar
            [
                'method' => 'post',
                'path' => '/services',
                'body' => [
                    'name' => 'bar',
                    'host' => 'bar.rethinkphp.com',
                ],
                'expectedStatus' => 201,
            ],

            // Trying to create a service with duplicated name
            [
                'method' => 'post',
                'path' => '/services',
                'body' => [
                    'name' => 'foo',
                    'host' => 'foo.rethinkphp.com',
                ],
                'expectedStatus' => 422,
                'expectedBody' => [
                    [
                        'message' => "The service 'foo' is already exists",
                        'field' => 'name',
                    ]
                ],
            ],

            // Updating service foo to foo2
            [
                'method' => 'put',
                'path' => '/services/foo',
                'body' => [
                    'name' => 'foo2',
                    'host' => 'foo2.rethinkphp.com',
                ],
                'expectedStatus' => 200,
                'expectedBody' => [
                    'name' => 'foo2',
                    'host' => 'foo2.rethinkphp.com',
                    'nodes' => [],
                    'routes' => [],
                ],
            ],

            // Deleting service bar
            [
                'method' => 'delete',
                'path' => '/services/bar',
                'expectedStatus' => 204,
            ],

            // Get all services
            [
                'method' => 'get',
                'path' => '/services',
                'expectedStatus' => 200,
                'expectedBody' => [
                    [
                        'name' => 'foo2',
                        'host' => 'foo2.rethinkphp.com',
                        'nodes' => [],
                        'routes' => [],
                    ]
                ],
            ],
        ];
    }


    public function testBasic()
    {
        foreach ($this->scenarios() as $scenario) {
            $this->runScenario($scenario);
        }
    }
}