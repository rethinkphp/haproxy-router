<?php

namespace rethink\hrouter\tests\services;

use PHPUnit_Framework_TestCase;
use rethink\hrouter\CfgGenerator;
use rethink\hrouter\entities\RouteEntity;

/**
 * Class CfgGeneratorTest
 *
 * @package rethink\hrouter\tests\services
 */
class CfgGeneratorTest extends PHPUnit_Framework_TestCase
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
^docs.rethinkphp.com/ service_docs
^docs.rethinkphp.com/overview service_docs
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
            return array_map([RouteEntity::class, 'fromArray'], $routes);
        }, $routeMaps);

        $routes = $generator->generateRoutes($routeMaps);

        $this->assertEquals($expected, $routes);
    }


    public function testGenerate()
    {
        $config = [
            'services' => [
                 [
                     'name' => 'rethinkphp',
                     'host' => 'rethinkphp.com',
                     'fullconn' => 100,
                     'nodes' => [
                         [
                             'name' => 'node_1',
                             'host' => '172.16.205.46:7788',
                             'check' => true,
                             'backup' => true,
                         ]
                     ],
                     'rewrites' => [
                         '/api/v1/(foo|bar)(.*)' => '/oapi/v1/\2\3',
                     ],
                ],
            ],
        ];

        $generator = new CfgGenerator($config);

        $conf = $generator->generate();

        $this->assertContains('server node_1 172.16.205.46:7788 check backup', $conf['haproxy.cfg']);
    }
}
