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

        if (in_array($method, ['get', 'delete'])) {
            $actor->$method($scenario['path']);
        } else {
            $actor->$method($scenario['path'], $scenario['body'] ?? []);
        }

        $actor->seeStatusCode($scenario['expectedStatus']);

        if (isset($scenario['expectedBody'])) {
            $actor->seeJsonEquals($scenario['expectedBody']);
        }
    }
}
