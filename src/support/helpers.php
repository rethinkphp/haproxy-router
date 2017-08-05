<?php

function normalize_path($path) {
    return array_reduce(explode('/', $path), function($a, $b) {
        if ($a === 0)
            $a = "/";

        if ($b === "" || $b === ".")
            return $a;

        if ($b === "..")
            return dirname($a);

        return preg_replace('/\/+/', "/", "$a/$b");
    }, 0);
}

function array_index(array $array, $key)
{
    $indexed = [];

    foreach ($array as $item) {
        $indexed[$item[$key]] = $item;
    }

    return $indexed;
}

/**
 * @return \rethink\hrouter\Haproxy
 */
function haproxy()
{
    return app('haproxy');
}

/**
 * @return \rethink\hrouter\services\Services
 */
function services()
{
    return app('services');
}

/**
 * @return \rethink\hrouter\services\Nodes
 */
function nodes()
{
    return app('nodes');
}

/**
 * @return \rethink\hrouter\services\Routes
 */
function routes()
{
    return app('routes');
}

/**
 * @return \rethink\hrouter\services\Domains
 */
function domains()
{
    return app('domains');
}

/**
 * @return \rethink\hrouter\services\Settings
 */
function settings()
{
    return app('settings');
}

/**
 * @return \rethink\hrouter\services\Challenges
 */
function challenges()
{
    return app('challenges');
}

/**
 * @return \rethink\hrouter\services\Acme
 */
function acme()
{
    return app('acme');
}

function whoami()
{
    return posix_getpwuid(posix_geteuid())['name'];
}

function root_privilege_is_required()
{
    if (whoami() !== 'root') {
        throw new \RuntimeException('Root privilege is required to install service');
    }
}

function pkg_config_is_required()
{
    system('which pkg-config >/dev/null', $retval);

    if ($retval !== 0) {
        throw new \RuntimeException('It seems pkg-config not missing from your system, please install it first');
    }
}

function get_systemd_unit_dir()
{
    ob_start();

    system('pkg-config systemd --variable=systemdsystemunitdir 2>&1', $retval);

    $output = ob_get_clean();

    if ($retval !== 0) {
        throw new \RuntimeException("Unable to get the directory of systemd unit files:\n" . $output);
    }

    return trim($output);
}

function env($name, $default = null)
{
    return getenv($name) ?: $default;
}
