<?php

namespace rethink\hrouter;


use blink\core\Object;
use blink\support\Json;

/**
 * Class Haproxy
 *
 * @package rethink\hrouter\services
 */
class Haproxy extends Object
{
    public $execFile = 'haproxy';
    public $configDir = __DIR__ . '/../runtime';

    public function init()
    {
        $this->configDir = normalize_path($this->configDir);
    }

    public function getConfigFile()
    {
        return $this->configDir . '/config.json';
    }

    public function getPidFile()
    {
        return $this->configDir . '/haproxy.pid';
    }

    /**
     * @return CfgApi
     */
    public function loadConfig()
    {
        $configFile = $this->getConfigFile();

        if (!file_exists($configFile)) {
            return new CfgApi();
        }

        $api = new CfgApi();
        $api->loadFile();

        return $api;
    }

    /**
     * Reload the running HAProxy instance
     *
     * @param boolean $reconfigure
     * @return boolean
     */
    public function reload($reconfigure = false)
    {
        $pidFile = $this->getPidFile();

        if (!file_exists($pidFile)) {
            return false;
        }

        if ($reconfigure) {
            $this->configure();
        }

        $pid = file_get_contents($pidFile);
        $pid = str_replace("\n", ' ', $pid);

        $command = sprintf(
            '%s -D -p %s -f %s -sf %s 2>&1',
            $this->execFile,
            $pidFile,
            $this->configDir . '/haproxy.cfg',
            $pid
        );

        exec($command, $output, $retval);

        return $retval;
    }

    protected function configure()
    {
        $cfgApi = $this->loadConfig();

        $config = $cfgApi->normalizedConfig();
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
        $pidFile = $this->getPidFile();

        if (file_exists($pidFile)) {
            return false;
        }

        $this->configure();

        $command = sprintf(
            '%s -D -p %s -f %s 2>&1',
            $this->execFile,
            $pidFile,
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
        $pidFile = $this->getPidFile();

        if (!file_exists($pidFile)) {
            return false;
        }

        $pids = explode("\n", trim(file_get_contents($pidFile)));

        foreach ($pids as $pid) {
            posix_kill($pid, 15);
        }

        unlink($pidFile);

        return true;
    }
}
