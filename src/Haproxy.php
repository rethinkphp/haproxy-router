<?php

namespace rethink\hrouter;


use blink\core\Object;
use blink\support\Json;
use rethink\hrouter\CfgGenerator;

/**
 * Class Haproxy
 *
 * @package rethink\hrouter\services
 */
class Haproxy extends Object
{
    public $pidFile = __DIR__ . '/../runtime/haproxy.pid';
    public $execFile = 'haproxy';
    public $configDir = __DIR__ . '/../runtime';

    public function init()
    {
        $this->pidFile = normalize_path($this->pidFile);
        $this->configDir = normalize_path($this->configDir);
    }

    public function getConfigFile()
    {
        return $this->configDir . '/config.json';
    }

    /**
     * @return array
     */
    public function loadConfig()
    {
        $configFile = $this->getConfigFile();

        if (!file_exists($configFile)) {
            return [];
        }

        $contents = file_get_contents($configFile);

        return Json::decode($contents);
    }

    /**
     * Reload the running HAProxy instance
     *
     * @param boolean $reconfigure
     * @return boolean
     */
    public function reload($reconfigure = false)
    {
        if (!file_exists($this->pidFile)) {
            return false;
        }

        if ($reconfigure) {
            $this->configure();
        }

        $pid = file_get_contents($this->pidFile);

        $command = sprintf(
            '%s -D -p %s -f %s -sf %s 2>&1',
            $this->execFile,
            $this->pidFile,
            $this->configDir . '/haproxy.cfg',
            $pid
        );

        exec($command, $output, $retval);

        return $retval;
    }

    protected function configure()
    {
        $config = $this->loadConfig();
        $config['configDir'] = $this->configDir;

        $gen = new CfgGenerator($config);

        $files = $gen->generate();

        foreach ($files as $name => $content) {
            $configFile = $this->configDir . '/' . $name;

            file_put_contents($configFile, $content);
        }
    }

    /**
     * Start HAProxy instance.
     *
     * @param null $output
     * @return mixed
     */
    public function start(&$output = null)
    {
        $this->configure();

        $command = sprintf(
            '%s -D -p %s -f %s 2>&1',
            $this->execFile,
            $this->pidFile,
            $this->configDir . '/haproxy.cfg'
        );

        exec($command, $output, $retval);

        return $retval;
    }

    /**
     * Stop the running HAProxy instance.
     *
     * @return boolean
     */
    public function stop()
    {
        if (file_exists($this->pidFile) && posix_kill((int)file_get_contents($this->pidFile), 15)) {
            unlink($this->pidFile);
            return true;
        }

        return false;
    }
}
