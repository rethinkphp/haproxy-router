<?php

namespace app\http\controllers;

use blink\core\Object;

class IndexController extends Object
{
    public function sayHello()
    {
        return 'Hello world, Blink.';
    }
}
