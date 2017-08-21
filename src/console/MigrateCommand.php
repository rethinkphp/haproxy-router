<?php

namespace rethink\hrouter\console;

use \Symfony\Component\Console\Input\InputInterface;
use \Symfony\Component\Console\Output\OutputInterface;
use blink\laravel\database\commands\MigrateCommand as BaseMigrateCommand;

/**
 * Class MigrateCommand
 *
 * @package rethink\hrouter\console
 */
class MigrateCommand extends BaseMigrateCommand
{
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $db = capsule();
        $conn = $db->connections[$db->default];

        if ($conn['driver'] === 'sqlite' && !file_exists($conn['database'])) {
            touch($conn['database']);
        }

        return parent::execute($input, $output);
    }
}
