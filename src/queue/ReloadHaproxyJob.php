<?php

namespace rethink\hrouter\queue;

/**
 * Class ReloadHaproxyJob
 *
 * @package rethink\hrouter\queue
 */
class ReloadHaproxyJob extends Job
{
    public $reconfigure = true;

    public function run()
    {
        return haproxy()->reload($this->reconfigure);
    }
}