<?php

namespace rethink\hrouter\restapi;

use blink\http\Request;
use rethink\hrouter\CfgApi;

/**
 * ServiceController class
 */
class ServiceController extends BaseController
{
    protected function getApi()
    {
        $api = new CfgApi();
        $api->loadFile();

        return $api;
    }

    public function index()
    {
        $api = $this->getApi();

        return $api->findServices();
    }

    public function create(Request $request)
    {
        $api = $this->getApi();

        $body = $request->getBody();

        $service = $api->createService(
            $body->get('name'),
            $body->get('host'),
            $body->except('name', 'host')
        );

        $api->persist();

        return $this->ok($service);
    }

    public function update($name, Request $request)
    {
        $api = $this->getApi();

        $service = $api->updateService($name, $request->body->all());

        $api->persist();

        return $this->ok($service, 200);
    }

    public function view($name)
    {
        $api = $this->getApi();

        return $api->findServiceByName($name);
    }

    public function delete($name)
    {
        $api = $this->getApi();

        $api->deleteService($name);

        $api->persist();

        return $this->noContent();
    }
}
