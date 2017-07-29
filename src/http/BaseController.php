<?php

namespace rethink\hrouter\http;

use blink\core\Object;
use blink\core\HttpException;
use blink\http\Request;
use blink\http\Response;

/**
 * Class BaseController
 *
 * @package rethink\hrouter\restapi
 */
class BaseController extends Object
{

    protected $request;
    protected $response;

    public function __construct(Request $request, Response $response, $config = [])
    {
        $this->request = $request;
        $this->response = $response;

        parent::__construct($config);
    }

    protected function ok($result, $status = 201)
    {
        $this->response->statusCode = $status;
        $this->response->data = $result;
    }

    protected function noContent()
    {
        $this->response->statusCode = 204;
    }

    protected function unauthorised($message = 'Unauthorised')
    {
        throw new HttpException(401, $message);
    }

    protected function badRequest($message = 'Bad request')
    {
        throw new HttpException(400, $message);
    }

    protected function forbid($message = 'Permission denied')
    {
        throw new HttpException(403, $message);
    }

    protected function notFound($message = 'Not Found')
    {
        throw new HttpException(404, $message);
    }
}