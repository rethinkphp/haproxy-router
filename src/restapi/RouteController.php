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
    public function index($serviceId)
    {
        $api = haproxy()->getCfgApi();

        $service = $api->findServiceOrFail($serviceId);

        return $api->findRoutes($service);
    }

    public function create($serviceId, Request $request)
    {
        $api = haproxy()->getCfgApi();

        $service = $api->findServiceOrFail($serviceId);

        $node = $api->addRoute($service, $request->body->all());

        $api->persist();

        haproxy()->reload(true);

        return $this->ok($node);
    }

    public function view($serviceId, $routeId)
    {
        $api = haproxy()->getCfgApi();

        $service = $api->findServiceOrFail($serviceId);

        return $api->findRoute($service, $routeId);
    }

    public function update($serviceId, $routeId, Request $request)
    {
        $api = haproxy()->getCfgApi();

        $service = $api->findServiceOrFail($serviceId);

        $route = $api->findRouteOrFail($service, $routeId);

        $node = $api->updateRoute($route, $request->body->all());

        $api->persist();

        haproxy()->reload(true);

        return $this->ok($node, 200);
    }

    public function delete($serviceId, $routeId)
    {
        $api = haproxy()->getCfgApi();

        $service = $api->findServiceOrFail($serviceId);

        $route = $api->findRouteOrFail($service, $routeId);

        $api->deleteRoute($route);

        $api->persist();

        haproxy()->reload(true);

        return $this->noContent();
    }
}