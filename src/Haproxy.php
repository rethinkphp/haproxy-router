<?php

namespace rethink\hrouter;

use blink\core\Object;

/**
 * Class Haproxy
 *
 * @package rethink\hrouter\services
 */
class Haproxy extends Object
{
    /**
     * Whether or not to run haproxy under a system supervisor, such as systemd.
     *
     * It is recommended to be true under production systems.
     *
     * @var bool
     */
    public $supervised = false;

    /**
     * The haproxy executable file paht.
     *
     * @var string
     */
    public $executable = 'haproxy';

    /**
     * The config dir used by haproxy.
     *
     * @var string
     */
    public $configDir = __DIR__ . '/../runtime';

    /**
     * Service control commands used to control haproxy service when `supervised` is enabled.
     *
     * @var array
     */
    public $commands = [];

    public $username = 'admin';
    public $password = 'haproxy-router';

    public function init()
    {
        $this->configDir = normalize_path($this->configDir);
    }

    public function getPidFile()
    {
        return $this->configDir . '/haproxy.pid';
    }

    /**
     * Reload the running HAProxy instance
     *
     * @param boolean $reconfigure
     * @return boolean
     */
    public function reload($reconfigure = false)
    {
        if ($reconfigure) {
            $this->configure();
        }

        if ($this->supervised) {
            exec($this->commands['reload'], $output, $retval);
            goto out;
        }

        $pidFile = $this->getPidFile();

        if (!file_exists($pidFile)) {
            return $this->start();
        }

        $pid = file_get_contents($pidFile);
        $pid = str_replace("\n", ' ', $pid);

        $command = sprintf(
            '%s -D -p %s -f %s -sf %s 2>&1',
            $this->executable,
            $pidFile,
            $this->configDir . '/haproxy.cfg',
            $pid
        );

        exec($command, $output, $retval);

out:
        return $this->logAndReturn('Failed to reload haproxy service', $retval, $output);
    }

    public function configure()
    {
        $gen = new CfgGenerator();

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

        if ($this->supervised) {
            exec($this->commands['start'], $output, $retval);
            goto out;
        }

        $pidFile = $this->getPidFile();

        if (file_exists($pidFile)) {
            return false;
        }

        $command = sprintf(
            '%s -D -p %s -f %s 2>&1',
            $this->executable,
            $pidFile,
            $this->configDir . '/haproxy.cfg'
        );

        exec($command, $output, $retval);

out:

        return $this->logAndReturn('Failed to start haproxy service', $retval, $output);
    }

    /**
     * Stop the running HAProxy instance.
     *
     * @return boolean
     */
    public function stop()
    {
        if ($this->supervised) {
            exec($this->commands['stop'], $output, $retval);
            return $this->logAndReturn('Failed to stop haproxy service', $retval, $output);
        }

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

    protected function logAndReturn($prefix, $retval, $output)
    {
        if ($retval !== 0) {
            logger()->error($prefix . ":\n" . implode("\n", $output));
        }

        return $retval;
    }
}
