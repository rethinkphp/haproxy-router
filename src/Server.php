<?php

namespace rethink\hrouter;

use blink\server\SwServer;

/**
 * Class Server
 */
class Server extends SwServer
{
    /**
     * The number of task workers should be started, default to zero.
     *
     * @var int
     */
    public $numTaskers;

    protected $sw;

    protected function normalizedConfig()
    {
        $config = parent::normalizedConfig();

        if ($this->numTaskers) {
            $config['task_worker_num'] = $this->numTaskers;
        }

        return $config;
    }

    public function onWorkerStart()
    {
        parent::onWorkerStart();

        if (func_get_arg(1) >= $this->numWorkers) {
            define('TASK_WORKER', true);

            app()->bootstrapIfNeeded();
            app()->server = $this;

            $this->sw->tick(1000, [queue(), 'run']);

            // trying to refresh certificates every 10 minutes
            $this->sw->tick(1000 * 600, [acme(), 'refreshCertificatesIfNeeded']);

            // trying to check updates every 60 minutes
            $this->sw->after(1000, [app('assets'), 'downloadAssetsIfNeeded']);
            $this->sw->tick(1000 * 3600, [app('assets'), 'downloadAssetsIfNeeded']);
        } else {
            define('TASK_WORKER', false);
        }
    }

    public function task($data)
    {
        return $this->sw->task($data);
    }

    public function onTask($server, $taskId, $fromId, $data)
    {
        queue()->insert($data);

        return true;
    }

    public function run()
    {
        $server = $this->sw = $this->createServer();
        $server->start();
    }
}
