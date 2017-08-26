<?php

namespace rethink\hrouter\console;

use blink\console\BaseServer;
use blink\core\console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ServiceInstallCommand
 *
 * @package rethink\hrouter\console
 */
class ServiceInstallCommand extends BaseServer
{
    public $name = 'service:install';
    public $description = 'Install haproxy-router as a system service';

    protected function configure()
    {
        $this->addOption('php', null, InputOption::VALUE_REQUIRED, 'Specify a custom php executable');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        root_privilege_is_required();
        pkg_config_is_required();

        $dist = get_systemd_unit_dir() . '/haproxy-router.service';

        file_put_contents($dist, $a = $this->getServiceConfig($input->getOption('php')));

        $envFile = '/etc/default/haproxy-router';
        if (!file_exists($envFile)) {
            file_put_contents($envFile, $this->getEnvConfig());
        }

        system('systemctl enable haproxy-router');

        $this->info('System service installed successfully.');
    }

    protected function getServiceConfig($php)
    {
        $tmpl = <<<'EOD'
[Unit]
Description=Haproxy Router

[Service]
EnvironmentFile=-/etc/default/haproxy-router
Type=forking
ExecStart={php}{bin_file} server:start
ExecReload={php}{bin_file} server:reload
ExecStop={php}{bin_file} server:stop
PIDFile={pid_file}
KillMode=process
Restart=on-failure

[Install]
WantedBy=multi-user.target
EOD;


        return strtr($tmpl, [
            '{php}' => $php ? $php . ' ' : '',
            '{bin_file}' => $this->blink->root . '/router',
            '{pid_file}' => $this->getPidFile(),
        ]);
    }

    protected function getEnvConfig()
    {
        return file_get_contents($this->blink->root . '/env.example');
    }
}
