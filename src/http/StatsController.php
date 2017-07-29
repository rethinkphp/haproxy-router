<?php

namespace rethink\hrouter\http;

use blink\http\Request;

/**
 * Class StatsController
 *
 * @package rethink\hrouter\restapi
 */
class StatsController extends BaseController
{

    public function update(Request $request)
    {
        $status = $request->body->get('server_status');

        if ($status == 'running') {
            haproxy()->start($errors);
            return $errors;
        } else if ($status == 'stopped') {
            haproxy()->stop();
        } else if ($status == 'reload') {
            haproxy()->reload();
        }

        return $this->noContent();
    }
}