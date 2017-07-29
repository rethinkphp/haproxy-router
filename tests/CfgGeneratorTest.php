<?php

namespace rethink\hrouter\tests\services;

use rethink\hrouter\CfgApi;
use rethink\hrouter\CfgGenerator;
use rethink\hrouter\entities\RouteEntity;
use rethink\hrouter\models\Route;
use rethink\hrouter\tests\TestCase;

/**
 * Class CfgGeneratorTest
 *
 * @package rethink\hrouter\tests\services
 */
class CfgGeneratorTest extends TestCase
{
    public function routeProvider()
    {
        return [
            [
                [
                    'docs' => [
                        [
                            'name' => 'api1',
                            'host' => 'docs.rethinkphp.com',
                            'path' => '/',
                        ],
                        [
                            'name' => 'api2',
                            'host' => 'docs.rethinkphp.com',
                            'path' => '/overview',
                        ],
                    ],
                ],
                <<<ROUTES
^docs.rethinkphp.com/overview service_docs
^docs.rethinkphp.com/ service_docs
ROUTES

            ]
        ];
    }

    /**
     * @dataProvider routeProvider
     */
    public function testGenerateRoutes($routeMaps, $expected)
    {
        $generator = new CfgGenerator();

        $routeMaps = array_map(function ($routes) {
            return array_map(function ($route) {
                return new Route($route);
            }, $routes);
        }, $routeMaps);

        $routes = $generator->generateRoutes($routeMaps);

        $this->assertEquals($expected, $routes);
    }


    public function testGenerate()
    {
        $service = services()->create([
            'name' => 'rethinkphp',
            'host' => 'rethinkphp.com',
            'fullconn' => 100,
            'rewrites' => [
                '/api/v1/(foo|bar)(.*)' => '/oapi/v1/\2\3',
            ],
        ]);

        nodes()->create([
            'service_id' => $service->id,
            'name' => 'node_1',
            'host' => '172.16.205.46:7788',
            'check' => true,
            'backup' => true,
        ]);

        $generator = new CfgGenerator([
            'configDir' => app()->runtime . '/tests',
        ]);

        $conf = $generator->generate();

        $this->assertContains('server node_1 172.16.205.46:7788 check backup', $conf['haproxy.cfg']);
    }
}
