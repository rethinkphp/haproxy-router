<?php

namespace rethink\hrouter\restapi\middleware;


use blink\core\MiddlewareContract;
use blink\http\Request;

/**
 * Class BasicAuth
 *
 * @package rethink\hrouter\restapi\middleware
 */
class BasicAuth implements MiddlewareContract
{
    /**
     * @param Request $owner
     */
    public function handle($owner)
    {
        $value = $owner->headers->first('Authorization');
        if (!$value) {
            return;
        }

        $parts = preg_split('/\s+/', $value);
        if (count($parts) < 2 && strtolower($parts[0]) != 'basic') {
            return;
        }

        $haproxy = haproxy();

        if (base64_decode($parts[1]) == $haproxy->username . ':' . $haproxy->password) {
            return;
        }

        response()->headers->set('WWW-Authenticate', 'Basic');
        abort(401);
    }
}

