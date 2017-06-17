<?php

namespace rethink\hrouter\restapi;

use blink\http\Request;
use rethink\hrouter\CfgApi;

/**
 * ServiceController class
 */
class ServiceController extends BaseController
{
    public function index()
    {
        $api = haproxy()->getCfgApi();

        return $api->findServices();
    }

    public function create(Request $request)
    {
        $api = haproxy()->getCfgApi();

        $body = $request->getBody();

        $service = $api->createService($body->all());

        $api->persist();

        haproxy()->reload(true);

        return $this->ok($service);
    }

    public function update($name, Request $request)
    {
        $api = haproxy()->getCfgApi();

        $service = $api->findService($name);

        if (!$service) {
            return $this->notFound();
        }

        $service = $api->updateService($service, $request->body->all());

        $api->persist();

        haproxy()->reload(true);

        return $this->ok($service, 200);
    }

    public function view($name)
    {
        $api = haproxy()->getCfgApi();

        return $api->findService($name);
    }

    public function delete($name)
    {
        $api = haproxy()->getCfgApi();

        $service = $api->findService($name);

        if (!$service) {
            return $this->notFound();
        }

        $api->deleteService($service);

        $api->persist();

        haproxy()->reload(true);

        return $this->noContent();
    }
}
