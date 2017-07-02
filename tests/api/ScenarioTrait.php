<?php

namespace rethink\hrouter\tests\api;

/**
 * Class ScenarioTrait
 *
 * @package rethink\hrouter\tests\api
 */
trait ScenarioTrait
{
    protected function runScenario($scenario)
    {
        $actor = $this->actor()
            ->withJson();

        $method = $scenario['method'] ?? 'get';

        $apiPrefix = '/api/v1';

        if (in_array($method, ['get', 'delete'])) {
            $actor->$method($apiPrefix . $scenario['path']);
        } else {
            $actor->$method($apiPrefix . $scenario['path'], $scenario['body'] ?? []);
        }

        $actor->seeStatusCode($scenario['expectedStatus']);

        if (isset($scenario['expectedBody'])) {
            $actor->seeJson($scenario['expectedBody']);
        }
    }
}
