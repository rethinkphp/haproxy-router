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

/**
 * @return \rethink\hrouter\queue\Queue
 */
function queue()
{
    return app('queue');
}

function get_existed_path($path)
{
    if (!file_exists($path)) {
        mkdir($path, 0777, true);
    }

    return $path;
}

function env($name, $default = null)
{
    $value = getenv($name);

    return $value !== false ? $value : $default;
}
