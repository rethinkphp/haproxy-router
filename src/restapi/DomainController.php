<?php

namespace rethink\hrouter\restapi;

use blink\http\Request;
use rethink\hrouter\CfgApi;

/**
 * DomainController class
 */
class DomainController extends BaseController
{
    public function index()
    {
        return domains()->queryAll();
    }

    public function create(Request $request)
    {
        $attributes = $request->body->all();

        $domain = domains()->create($attributes);

        return $this->ok($domain);
    }

    public function update($name, Request $request)
    {
        $domain = domains()->update($name, $request->body->all());

        haproxy()->reload(true);

        return $this->ok($domain, 200);
    }

    public function view($name)
    {
        return domains()->loadOrFail($name);
    }

    public function delete($name)
    {
        domains()->delete($name);

        haproxy()->reload(true);

        return $this->noContent();
    }
}
