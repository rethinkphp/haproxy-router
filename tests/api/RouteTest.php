<?php

namespace rethink\hrouter\tests\api;

use rethink\hrouter\tests\TestCase;

/**
 * Class RouteTest
 *
 * @package rethink\hrouter\tests\api
 */
class RouteTest extends TestCase
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
            [
                'method' => 'post',
                'path' => '/services',
                'body' => [
                    'name' => 'rethink',
                    'host' => 'rethinkphp.com',
                ],
                'expectedStatus' => 201,
            ],

            [
                'method' => 'get',
                'path' => '/services/rethink/routes',
                'expectedStatus' => 200,
                'expectedBody' => [],
            ],

            [
                'method' => 'post',
                'path' => '/services/rethink/routes',
                'body' => [
                    'name' => 'route1',
                    'host' => '127.0.0.1:8899',
                ],
                'expectedStatus' => 201,
            ],

            [
                'method' => 'post',
                'path' => '/services/rethink/routes',
                'body' => [
                    'name' => 'route2',
                    'host' => '127.0.0.1:8889',
                ],
                'expectedStatus' => 201,
            ],

            // Create a route with duplicated name
            [
                'method' => 'post',
                'path' => '/services/rethink/routes',
                'body' => [
                    'name' => 'route2',
                    'host' => '127.0.0.1:8889',
                ],
                'expectedStatus' => 422,
                'expectedBody' => [
                    [
                        'field' => 'name',
                        'message' => "The route 'route2' is already exists",
                    ]
                ],
            ],

            // Updating a route1's host
            [
                'method' => 'put',
                'path' => '/services/rethink/routes/route1',
                'body' => [
                    'host' => '127.0.0.2:80',
                ],
                'expectedStatus' => 200,
            ],

            // Deleting route2
            [
                'method' => 'delete',
                'path' => '/services/rethink/routes/route2',
                'expectedStatus' => 204,
            ],

            // Getting all routes
            [
                'method' => 'get',
                'path' => '/services/rethink/routes',
                'expectedStatus' => 200,
                'expectedBody' => [
                    [
                        'name' => 'route1',
                        'host' => '127.0.0.2:80',
                    ]
                ],
            ]
        ];
    }

    public function testBasic()
    {
        foreach ($this->scenarios() as $i => $scenario) {
            $this->runScenario($scenario);
        }
    }
}