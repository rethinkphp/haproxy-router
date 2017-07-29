<?php

namespace rethink\hrouter\http\middleware;


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
     * @param Request $request
     */
    public function handle($request)
    {
        $value = $request->headers->first('Authorization');
        if (!$value) {
            goto error;
        }

        $parts = preg_split('/\s+/', $value);
        if (count($parts) < 2 && strtolower($parts[0]) != 'basic') {
            goto error;
        }

        $haproxy = haproxy();

        if (base64_decode($parts[1]) == $haproxy->username . ':' . $haproxy->password) {
            return;
        }
error:
        response()->headers->set('WWW-Authenticate', 'Basic');
        abort(401);
    }
}

