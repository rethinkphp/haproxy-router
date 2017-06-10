<?php

namespace rethink\hrouter\tests\api;

use rethink\hrouter\tests\TestCase;

/**
 * Class NodeTest
 *
 * @package rethink\hrouter\tests\api
 */
class NodeTest extends TestCase
{
    use ScenarioTrait;

    public function setUp()
    {
        parent::setUp();

        @unlink(haproxy()->getConfigFile());
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
                'path' => '/services/rethink/nodes',
                'expectedStatus' => 200,
                'expectedBody' => [],
            ],

            [
                'method' => 'post',
                'path' => '/services/rethink/nodes',
                'body' => [
                    'name' => 'node1',
                    'host' => '127.0.0.1:8899',
                ],
                'expectedStatus' => 201,
            ],

            [
                'method' => 'post',
                'path' => '/services/rethink/nodes',
                'body' => [
                    'name' => 'node2',
                    'host' => '127.0.0.1:8889',
                ],
                'expectedStatus' => 201,
            ],

            // Create a node with duplicated name
            [
                'method' => 'post',
                'path' => '/services/rethink/nodes',
                'body' => [
                    'name' => 'node2',
                    'host' => '127.0.0.1:8889',
                ],
                'expectedStatus' => 422,
                'expectedBody' => [
                    [
                        'field' => 'name',
                        'message' => "The node 'node2' is already exists",
                    ]
                ],
            ],

            // Updating a node1's host
            [
                'method' => 'put',
                'path' => '/services/rethink/nodes/node1',
                'body' => [
                    'host' => '127.0.0.2:80',
                ],
                'expectedStatus' => 200,
            ],

            // Deleting node2
            [
                'method' => 'delete',
                'path' => '/services/rethink/nodes/node2',
                'expectedStatus' => 204,
            ],

            // Getting all nodes
            [
                'method' => 'get',
                'path' => '/services/rethink/nodes',
                'expectedStatus' => 200,
                'expectedBody' => [
                    [
                        'name' => 'node1',
                        'host' => '127.0.0.2:80',
                    ]
                ],
            ]
        ];
    }

    public function testBasic()
    {
        foreach ($this->scenarios() as $scenario) {
            $this->runScenario($scenario);
        }
    }
}