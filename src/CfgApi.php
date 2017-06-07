<?php

namespace rethink\hrouter;

use blink\core\InvalidParamException;
use blink\core\Object;
use blink\support\Json;
use rethink\hrouter\entities\NodeEntity;
use rethink\hrouter\entities\ServiceEntity;
use rethink\hrouter\support\ValidationException;

/**
 * Class CfgApi
 *
 * @package rethink\hrouter
 */
class CfgApi extends Object
{
    private $_config = [];

    public function loadFile()
    {
        $configFile = app()->runtime . '/config.json';

        if (!file_exists($configFile)) {
            return;
        }

        $contents = file_get_contents($configFile);

        $this->_config = $this->indexConfig(Json::decode($contents));
    }

    protected function indexConfig($config)
    {
        $config['services'] = array_index($config['services'] ?? [], 'name');
        $config['services'] = array_map([ServiceEntity::class, 'fromArray'], $config['services']);

        foreach ($config['services'] as &$service) {
            if (isset($service['nodes'])) {
                $service['nodes'] = array_index($service['nodes'], 'name');
                $service['nodes'] = array_map([NodeEntity::class, 'fromArray'], $service['nodes']);
            }
        }

        return $config;
    }

    protected function normalizeConfig($config)
    {
        $config['services'] = array_values($config['services'] ?? []);

        foreach ($config['services'] as &$service) {
            if (isset($service['nodes'])) {
                $service['nodes'] = array_values($service['nodes']);
            }
        }
        return $config;
    }

    /**
     * @return array
     */
    public function findServices()
    {
        return array_values($this->_config['services']  ?? []);
    }

    /**
     * @param $name
     * @return bool
     */
    public function hasService($name)
    {
        return isset($this->_config['services'][$name]);
    }

    /**
     * @param $name
     * @return array|null
     */
    public function findService($name)
    {
        return $this->_config['services'][$name] ?? null;
    }

    /**
     * @param $name
     * @return ServiceEntity
     */
    public function findServiceForUpdate($name)
    {
        if (!$this->hasService($name)) {
            throw new InvalidParamException("The service '$name' does not exists");
        }

        return $this->_config['services'][$name];
    }

    /**
     * Create a new service.
     *
     * @param $name
     * @param $host
     * @param array $params
     * @return array
     * @throws ValidationException
     */
    public function createService($name, $host, $params = [])
    {
        if ($this->findService($name)) {
            throw ValidationException::fromArgs('name', "The service '$name' is already exists");
        }

        $params['name'] = $name;
        $params['host'] = $host;

        $this->_config['services'][] = $params;

        return $params;
    }

    public function updateService($name, array $params)
    {
        $service = $this->findServiceForUpdate($name);

        if (!$service) {
            throw new InvalidParamException("The service '$name' does not exists");
        }

        $service->merge($params);

        return $service;
    }

    public function deleteService($name)
    {
        unset($this->_config['services'][$name]);
    }

    /**
     * @param string $serviceName
     * @return array
     */
    public function findNodes(string $serviceName)
    {
        if (!$this->hasService($serviceName)) {
            throw new InvalidParamException("The service '$serviceName' does not exists");
        }

        $service = $this->findService($serviceName);

        return array_values($service['nodes'] ?? []);
    }

    public function findNode(string $serviceName, string $nodeName)
    {
        if (!($service = $this->findService($serviceName))) {
            throw new InvalidParamException("The service '$serviceName' does not exists");
        }

        return $service['nodes'][$nodeName] ?? null;
    }

    /**
     * @param string $serviceName
     * @param string $nodeName
     * @return NodeEntity
     */
    public function findNodeForUpdate(string $serviceName, string $nodeName)
    {
        $service = $this->findServiceForUpdate($serviceName);

        if (!isset($service['nodes'][$nodeName])) {
            throw new InvalidParamException("The node '$nodeName' does not exists");
        }

        return $service['nodes'][$nodeName];
    }
    /**
     * @param $serviceName
     * @param $nodeName
     * @param array $def
     * @return NodeEntity
     * @throws ValidationException
     */
    public function addNode(string $serviceName, string $nodeName, array $def)
    {
        $service = $this->findServiceForUpdate($serviceName);

        if (isset($service['nodes'][$nodeName])) {
            throw ValidationException::fromArgs('name', "The node '$nodeName' is already exists");
        }

        $def['name'] = $nodeName;

        $node = NodeEntity::fromArray($def);

        return $service['nodes'][$nodeName] = $node;
    }


    public function updateNode(string $serviceName, string $nodeName, array $def)
    {
        $node = $this->findNodeForUpdate($serviceName, $nodeName);

        $node->merge($def);

        return $node;
    }

    public function deleteNode(string $serviceName, string $nodeName)
    {
        $service = $this->findServiceForUpdate($serviceName);

        unset($service['nodes'][$nodeName]);
    }

    public function persist()
    {
        $config = $this->normalizeConfig($this->_config);

        file_put_contents(app()->runtime . '/config.json', Json::encode($config));
    }
}