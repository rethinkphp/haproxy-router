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
