<?php

namespace app\tests;

/**
 * Class ExampleTest
 *
 * @package app\tests
 */
class ExampleTest extends TestCase
{
    public function testExample()
    {
        $this->actor()
            ->get('/')
            ->seeStatusCode(200)
            ->seeContent('Hello world, Blink.');
    }
}
