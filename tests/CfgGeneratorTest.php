<?php

namespace rethink\hrouter\tests\services;

use PHPUnit_Framework_TestCase;
use rethink\hrouter\CfgGenerator;

/**
 * Class CfgGeneratorTest
 *
 * @package rethink\hrouter\tests\services
 */
class CfgGeneratorTest extends PHPUnit_Framework_TestCase
{
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
