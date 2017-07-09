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
        $api = haproxy()->getCfgApi();

        return $api->findDomains();
    }

    public function create(Request $request)
    {
        $api = haproxy()->getCfgApi();

        $body = $request->getBody();

        $domain = $api->createDomain($body->all());

        $api->persist();

        haproxy()->reload(true);

        return $this->ok($domain);
    }

    public function update($name, Request $request)
    {
        $api = haproxy()->getCfgApi();

        $domain = $api->findDomain($name);

        if (!$domain) {
            return $this->notFound();
        }

        $domain = $api->updateDomain($domain, $request->body->all());

        $api->persist();

        haproxy()->reload(true);

        return $this->ok($domain, 200);
    }

    public function view($name)
    {
        $api = haproxy()->getCfgApi();

        return $api->findDomain($name);
    }

    public function delete($name)
    {
        $api = haproxy()->getCfgApi();

        $domain = $api->findDomain($name);

        if (!$domain) {
            return $this->notFound();
        }

        $api->deleteDomain($domain);

        $api->persist();

        haproxy()->reload(true);

        return $this->noContent();
    }
}
