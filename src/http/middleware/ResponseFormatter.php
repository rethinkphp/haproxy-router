<?php

namespace rethink\hrouter\http\middleware;

use blink\core\MiddlewareContract;
use rethink\hrouter\support\ValidationException;

/**
 * Class ResponseFormatter
 *
 * @package chalk\http\middleware
 */
class ResponseFormatter implements MiddlewareContract
{
    /**
     * @param \blink\http\Response $response
     */
    public function handle($response)
    {
        if ($response->data instanceof ValidationException) {
            $response->data = $response->data->errors;
            $response->status(422, 'Validation Error');
        } else if (!$response->headers->has('Content-Type')) {
            $response->headers->set('Content-Type', 'application/json');
        }
    }
}
