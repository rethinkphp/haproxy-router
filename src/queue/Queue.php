<?php

namespace rethink\hrouter\queue;

use blink\server\SwServer;
use Psy\Util\Json;
use SplPriorityQueue;
use blink\core\Object;

/**
 * Class Queue
 *
 * @package rethink\hrouter\services
 */
class Queue extends Object
{
    protected $jobs;
    protected $lastInserts = [];

    public function init()
    {
        $this->jobs = new SplPriorityQueue();
        $this->jobs->setExtractFlags(SplPriorityQueue::EXTR_BOTH);
    }

    protected function getLastInsert($name)
    {
        return $this->lastInserts[$name] ?? 0;
    }

    protected function freshLastInsert($name, $time)
    {
        $this->lastInserts[$name] = $time;
    }

    public function insert(Job $job)
    {
        $now = time();
        $name = get_class($job);

        if ($now - $this->getLastInsert($name) > $job->interval) {
            $this->jobs->insert($job, $now + $job->interval);
            $this->freshLastInsert($name, $now);
        }
    }

    public function push(Job $job)
    {
        $server = app()->server;

        if (!$server instanceof SwServer) {
            return;
        }

        if (TASK_WORKER) {
            $this->insert($job);
        } else {
            $server->task($job);
        }
    }

    public function run()
    {
        $now = time();

        while (!$this->jobs->isEmpty()) {
            $top = $this->jobs->top();

            if ($top['priority'] > $now) {
                break;
            }

            $this->jobs->extract();

            $this->execute($top['data']);
        }
    }

    protected function execute(Job $job)
    {
        try {
            $result = $job->run();

            if ($result === null || $result === 0 || $result === true) {
                logger()->info(sprintf('Job %s%s executed successfully', get_class($job), Json::encode($job)));
            } else {
                logger()->error(sprintf('Job %s%s execution failure with status: %s', get_class($job), Json::encode($job), $result));
            }
        } catch (\Exception $e) {
            logger()->error($e);
        }
    }
}
