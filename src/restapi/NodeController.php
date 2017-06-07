<?php

namespace rethink\hrouter\restapi;

use blink\http\Request;
use rethink\hrouter\CfgApi;

/**
 * NodeController class
 */
class NodeController extends BaseController
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

        return $api->findNodes($serviceId);
    }

    public function create($serviceId, Request $request)
    {
        $api = $this->getApi();

        $node = $api->addNode(
            $serviceId,
            $request->body->get('name'),
            $request->body->except('name')
        );

        $api->persist();

        return $this->ok($node);
    }

    public function view($serviceId, $nodeId)
    {
        $api = $this->getApi();

        return $api->findNode($serviceId, $nodeId);
    }

    public function update($serviceId, $nodeId, Request $request)
    {
        $api = $this->getApi();

        $node = $api->updateNode($serviceId, $nodeId, $request->body->all());

        $api->persist();

        return $this->ok($node, 200);
    }

    public function delete($serviceId, $nodeId)
    {
        $api = $this->getApi();

        $api->deleteNode($serviceId, $nodeId);

        $api->persist();

        return $this->noContent();
    }
}
