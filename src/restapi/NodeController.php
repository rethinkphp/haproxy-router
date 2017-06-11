<?php

namespace rethink\hrouter\restapi;

use blink\http\Request;
use rethink\hrouter\CfgApi;

/**
 * NodeController class
 */
class NodeController extends BaseController
{

    public function index($serviceId)
    {
        $api = haproxy()->getCfgApi();

        return $api->findNodes($serviceId);
    }

    public function create($serviceId, Request $request)
    {
        $api = haproxy()->getCfgApi();

        $node = $api->addNode(
            $serviceId,
            $request->body->get('name'),
            $request->body->except('name')
        );

        $api->persist();

        haproxy()->reload(true);

        return $this->ok($node);
    }

    public function view($serviceId, $nodeId)
    {
        $api = haproxy()->getCfgApi();

        return $api->findNode($serviceId, $nodeId);
    }

    public function update($serviceId, $nodeId, Request $request)
    {
        $api = haproxy()->getCfgApi();

        $node = $api->updateNode($serviceId, $nodeId, $request->body->all());

        $api->persist();

        haproxy()->reload(true);

        return $this->ok($node, 200);
    }

    public function delete($serviceId, $nodeId)
    {
        $api = haproxy()->getCfgApi();

        $api->deleteNode($serviceId, $nodeId);

        $api->persist();

        haproxy()->reload(true);

        return $this->noContent();
    }
}
