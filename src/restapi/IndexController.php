<?php

namespace rethink\hrouter\restapi;

use blink\core\Object;

class IndexController extends Object
{
    public function sayHello()
    {
        return 'Hello world, Blink.';
    }
}
