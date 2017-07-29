<?php

namespace rethink\hrouter\tests\api;

use rethink\hrouter\tests\TestCase;

/**
 * Class DomainTest
 *
 * @package rethink\hrouter\tests\api
 */
class DomainTest extends TestCase
{
    use ScenarioTrait;

    public function scenarios()
    {
        return [
            // Create a domain
            [
                'method' => 'post',
                'path' => '/domains',
                'body' => [
                    'name' => 'www.rethinkphp.com',
                    'description' => 'www',
                ],
                'expectedStatus' => 201,
            ],

            // Create a domain
            [
                'method' => 'post',
                'path' => '/domains',
                'body' => [
                    'name' => 'blog.rethinkphp.com',
                    'description' => 'blog',
                ],
                'expectedStatus' => 201,
            ],

            // Trying to create a domain with duplicated name
            [
                'method' => 'post',
                'path' => '/domains',
                'body' => [
                    'name' => 'blog.rethinkphp.com',
                    'description' => 'blog',
                ],
                'expectedStatus' => 422,
                'expectedBody' => [
                    [
                        'message' => "The name has already been taken.",
                        'field' => 'name',
                    ]
                ],
            ],

            // Updating domain
            [
                'method' => 'put',
                'path' => '/domains/www.rethinkphp.com',
                'body' => [
                    'description' => 'updated description',
                ],
                'expectedStatus' => 200,
                'expectedBody' => [
                    'description' => 'updated description',
                ],
            ],

            // Deleting domain
            [
                'method' => 'delete',
                'path' => '/domains/www.rethinkphp.com',
                'expectedStatus' => 204,
            ],

            // Get all domains
            [
                'method' => 'get',
                'path' => '/domains',
                'expectedStatus' => 200,
                'expectedBody' => [
                    'name' => 'blog.rethinkphp.com',
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