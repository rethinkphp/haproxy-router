<?php

namespace rethink\hrouter\console;

use blink\core\console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ServiceUninstallCommand
 *
 * @package rethink\hrouter\console
 */
class ServiceUninstallCommand extends Command
{
    public $name = 'service:uninstall';
    public $description = 'Uninstall haproxy-router service from the system';

    public function execute(InputInterface $input, OutputInterface $output)
    {
        root_privilege_is_required();
        pkg_config_is_required();

        $dist = get_systemd_unit_dir() . '/haproxy-router.service';

        if (!file_exists($dist)) {
           return;
        }

        system('systemctl disable haproxy-router');

        unlink($dist);

        $this->info('System service uninstalled successfully.');
    }
}
