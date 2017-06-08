<?php

namespace rethink\hrouter\restapi;

use rethink\hrouter\CfgApi;
use blink\http\Request;

/**
 * Class RouteController
 *
 * @package rethink\hrouter\restapi
 */
class RouteController extends BaseController
{
    protected function getApi()
    {
        $api = new CfgApi();
        $api->loadFile();

        return $api;
    }

    public function index($serviceId)
    {
        $api = $this->getApi();

        return $api->findRoutes($serviceId);
    }

    public function create($serviceId, Request $request)
    {
        $api = $this->getApi();

        $node = $api->addRoute(
            $serviceId,
            $request->body->get('name'),
            $request->body->except('name')
        );

        $api->persist();

        haproxy()->reload(true);

        return $this->ok($node);
    }

    public function view($serviceId, $routeId)
    {
        $api = $this->getApi();

        return $api->findRoute($serviceId, $routeId);
    }

    public function update($serviceId, $routeId, Request $request)
    {
        $api = $this->getApi();

        $node = $api->updateRoute($serviceId, $routeId, $request->body->all());

        $api->persist();

        haproxy()->reload(true);

        return $this->ok($node, 200);
    }

    public function delete($serviceId, $routeId)
    {
        $api = $this->getApi();

        $api->deleteRoute($serviceId, $routeId);

        $api->persist();

        haproxy()->reload(true);

        return $this->noContent();
    }
}