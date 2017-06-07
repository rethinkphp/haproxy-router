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

