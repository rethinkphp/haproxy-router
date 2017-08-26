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
            app()->bootstrapIfNeeded();

            // trying to refresh certificates every 10 minutes
            $this->sw->tick(1000 * 600, [acme(), 'refreshCertificatesIfNeeded']);
        }
    }

    public function run()
    {
        $server = $this->sw = $this->createServer();
        $server->start();
    }
}
