<?php

namespace rethink\hrouter\queue;

/**
 * Class Job
 *
 * @package rethink\hrouter\queue
 */
abstract class Job {

    public $interval = 5;

    abstract public function run();
}
