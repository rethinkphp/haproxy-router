<?php
return [
    /*------------------------------------------------------------------
     * The Application Name
     *------------------------------------------------------------------
     *
     * Here you specifies the name of your application.
     *
     */

    'name' => 'haproxy-router',

    /*------------------------------------------------------------------
     * Application Root Path
     *------------------------------------------------------------------
     *
     * Specify the root path of your application.
     *
     */

    'root' => realpath(__DIR__ . '/../..'),


    /*------------------------------------------------------------------
     * Application Timezone
     *------------------------------------------------------------------
     *
     * Specify the default timezone for your application, which will be
     * used by the PHP date and data-time functions.
     *
     */

    'timezone' => env('timezone', 'UTC'),

    /*------------------------------------------------------------------
     * Application Debug Mode
     * -----------------------------------------------------------------
     *
     * When debug mode is enabled, detailed error message with stack traces
     * will be shown on every error in your application, you should disable
     * this in production environment.
     *
     */

    'debug' => (boolean)env('debug', 1),

    /*------------------------------------------------------------------
     * Application Environment
     * -----------------------------------------------------------------
     *
     * Specify what is the environment your application is running on.
     * This config now is mainly used for testing purpose, in unit tests,
     * PHPUnit will set environment to `test` automatically.
     *
     */

    'environment' => BLINK_ENV,

    /*------------------------------------------------------------------
     * Application Runtime Path
     *-----------------------------------------------------------------
     *
     * Specify the runtime path that used to store generated temporary
     * files, you should make sure the directory is writable by Blink
     * processes.
     *
     */

    'runtime' => env('runtime_dir', normalize_path(__DIR__ . '/../../runtime')),

    /*------------------------------------------------------------------
     * Default Controller Namespace
     *------------------------------------------------------------------
     *
     * The default namespace of your controllers, with this configured,
     * you can write relative controller class name in your routes configuration
     * file.
     *
     */

    'controllerNamespace' => '\rethink\hrouter\http',

    /*------------------------------------------------------------------
     * Application Commands
     *------------------------------------------------------------------
     *
     * Registering console commands used by the application.
     *
     */
    'commands' => [
        rethink\hrouter\console\MigrateCommand::class,
        blink\laravel\database\commands\MakeCommand::class,
        blink\laravel\database\commands\ResetCommand::class,
        [
            'class' => blink\console\ServiceInstallCommand::class,
            'bin' => 'router',
        ],
        blink\console\ServiceUninstallCommand::class,
    ],
];
