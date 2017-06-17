<?php

namespace rethink\hrouter;

use blink\core\HttpException;
use blink\core\InvalidParamException;
use blink\core\Object;
use blink\support\Json;
use rethink\hrouter\entities\BaseEntity;
use rethink\hrouter\entities\NodeEntity;
use rethink\hrouter\entities\RouteEntity;
use rethink\hrouter\entities\ServiceEntity;
use rethink\hrouter\support\ValidationException;

/**
 * Class CfgApi
 *
 * @package rethink\hrouter
 */
class CfgApi extends Object
{
    public $configFile;

    protected $_store = [];

    public function init()
    {
        if (!file_exists($this->configFile)) {
            return;
        }

        $contents = file_get_contents($this->configFile);

        $this->_store = $this->indexStore(Json::decode($contents));
    }

    protected function indexStore($data)
    {
        $map = [
            'services' => ServiceEntity::class,
            'nodes' => NodeEntity::class,
            'routes' => RouteEntity::class,
        ];

        foreach ($data as $storeName => $rows) {
            if (isset($map[$storeName])) {
                $data[$storeName] = array_map([$map[$storeName], 'fromArray'], $rows);
            }
        }

        return $data;
    }


    protected function findAll($storeName, array $conditions = [])
    {
        $rows = $this->_store[$storeName] ?? [];

        if ($conditions === []) {
            return $rows;
        }

        return $this->filterConditions($rows, $conditions);
    }

    protected function filterConditions(array $rows, array $conditions)
    {
        $filtered = [];

        foreach ($rows as $row) {
            $matches = true;

            foreach ($conditions as $key => $value) {
                if (!isset($row[$key]) || !in_array($row[$key], (array)$value)) {
                    $matches = false;
                    break;
                }
            }

            if ($matches) {
                $filtered[] = $row;
            }
        }

        return $filtered;
    }

    public function addEntity($storeName, BaseEntity $entity)
    {
        $this->_store[$storeName][] = $entity;
    }

    public function removeEntity($storeName, BaseEntity $entity)
    {
        if (!isset($this->_store[$storeName])) {
            return 0;
        }

        $removed = 0;
        $rows = $this->_store[$storeName];

        foreach ($rows as $key => $row) {
            if ($entity === $row) {
                unset($rows[$key]);
                $removed ++;
            }
        }

        if ($removed) {
            $this->_store[$storeName] = array_values($rows);
        }

        return $removed;
    }

    /**
     * @return array
     */
    public function findServices()
    {
        return $this->findAll('services');
    }

    /**
     * @param $name
     * @return bool
     */
    public function hasService($name)
    {
        $services = $this->findAll('services', ['name' => $name]);

        return count($services) > 0;
    }

    /**
     * @param $name
     * @return array|null
     */
    public function findService($name)
    {
        $services = $this->findAll('services', ['name' => $name]);

        return $services[0] ?? null;
    }

    public function findServiceOrFail($name)
    {
        $service = $this->findService($name);

        if (!$service) {
            // TODO we should not throw http exception here
            return new HttpException(404);
        }

        return $service;
    }

    /**
     * Create a new service.
     *
     * @param array $params
     * @return ServiceEntity
     * @throws ValidationException
     */
    public function createService($params = [])
    {
        $name = $params['name'];

        if ($this->findService($name)) {
            throw ValidationException::fromArgs('name', "The service '$name' is already exists");
        }

        $params['id'] = uniqid();

        $service = ServiceEntity::fromArray($params);

        $this->addEntity('services', $service);

        return $service;
    }

    public function updateService(ServiceEntity $service, array $params)
    {
        $service->merge($params);

        return $service;
    }

    public function deleteService(ServiceEntity $service)
    {
        return $this->removeEntity('services', $service);
    }

    public function findAllRoutes()
    {
        return $this->findAll('routes');
    }

    public function findRoutes(ServiceEntity $service)
    {
        return $this->findAll('routes', ['service_id' => $service->id]);
    }

    public function findRoute(ServiceEntity $service, string $routeName)
    {
        $routes = $this->findAll('routes', ['service_id' => $service->id, 'name' => $routeName]);

        return $routes[0] ?? null;
    }

    public function findRouteOrFail(ServiceEntity $service, $name)
    {
        $route = $this->findRoute($service, $name);

        if (!$route) {
            // TODO we should not throw http exception here
            return new HttpException(404);
        }

        return $route;
    }

    /**
     * @param $service
     * @param array $routeDef
     * @return RouteEntity
     * @throws ValidationException
     */
    public function addRoute(ServiceEntity $service, $routeDef)
    {
        $routeDef['id'] = uniqid();
        $routeDef['service_id'] = $service->id;

        if ($this->findRoute($service, $routeDef['name'])) {
            throw ValidationException::fromArgs('name', "The route '{$routeDef['name']}' is already exists");
        }

        $route = RouteEntity::fromArray($routeDef);

        $this->addEntity('routes', $route);

        return $route;
    }

    public function updateRoute(RouteEntity $route, array $def)
    {
        $route->merge($def);

        return $route;
    }

    public function deleteRoute(RouteEntity $route)
    {
        return $this->removeEntity('routes', $route);
    }

    /**
     * @param $service
     * @return array
     */
    public function findNodes(ServiceEntity $service)
    {
        return $this->findAll('nodes', ['service_id' => $service->id]);
    }

    public function findNode(ServiceEntity $service, string $nodeName)
    {
        $nodes = $this->findAll('nodes', ['service_id' => $service->id, 'name' => $nodeName]);

        return $nodes[0] ?? null;
    }

    public function findNodeOrFail(ServiceEntity $service, $name)
    {
        $node = $this->findNode($service, $name);

        if (!$node) {
            // TODO we should not throw http exception here
            return new HttpException(404);
        }

        return $node;
    }

    /**
     * @param $service
     * @param array $def
     * @return NodeEntity
     * @throws ValidationException
     */
    public function addNode(ServiceEntity $service, array $def)
    {
        $def['id'] = uniqid();
        $def['service_id'] = $service->id;

        if ($this->findNode($service, $def['name'])) {
            throw ValidationException::fromArgs('name', "The node '{$def['name']}' is already exists");
        }

        $node = NodeEntity::fromArray($def);

        $this->addEntity('nodes', $node);

        return $node;
    }

    public function updateNode(NodeEntity $node, array $def)
    {
        $node->merge($def);

        return $node;
    }

    public function deleteNode(NodeEntity $node)
    {
        return $this->removeEntity('nodes', $node);
    }

    public function options()
    {
        return $this->_store['options'] ?? [];
    }

    public function option($name)
    {
        $options = $this->_store['options'] ?? [];

        return $options[$name] ?? null;
    }

    public function persist()
    {
        file_put_contents(haproxy()->getConfigFile(), Json::encode($this->_store));
    }
}