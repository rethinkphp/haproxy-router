<?php

namespace rethink\hrouter\http;

use blink\http\Request;
use rethink\hrouter\CfgApi;

/**
 * NodeController class
 */
class NodeController extends BaseController
{

    public function index($serviceId)
    {
        $service = services()->loadOrFail($serviceId);

        return $service->nodes;
    }

    public function create($serviceId, Request $request)
    {
        $service = services()->loadOrFail($serviceId);

        $attributes = $request->body->all();
        $attributes['service_id'] = $service->id;

        $node = nodes()->create($attributes);

        haproxy()->reload(true);

        return $this->ok($node);
    }

    public function view($serviceId, $nodeId)
    {
        return nodes()->loadInServiceOrFail($serviceId, $nodeId);
    }

    public function update($serviceId, $nodeId, Request $request)
    {
        $node = nodes()->loadInServiceOrFail($serviceId, $nodeId);

        $node = nodes()->update($node, $request->body->all());

        haproxy()->reload(true);

        return $this->ok($node, 200);
    }

    public function delete($serviceId, $nodeId)
    {
        $node = nodes()->loadInServiceOrFail($serviceId, $nodeId);

        nodes()->delete($node);

        haproxy()->reload(true);

        return $this->noContent();
    }
}
