<?php

namespace rethink\hrouter;

use blink\core\InvalidParamException;
use blink\core\Object;
use blink\support\Json;
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

        $this->_config = Json::decode($contents);
    }

    /**
     * @return array
     */
    public function findServices()
    {
        return $this->_config['services']  ?? [];
    }

    /**
     * @param $name
     * @return array|null
     */
    public function findServiceByName($name)
    {
        foreach ($this->findServices() as $service) {
            if ($service['name'] == $name) {
                return $service;
            }
        }
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
        if ($this->findServiceByName($name)) {
            throw ValidationException::fromArgs('name', "The service '$name' is already exists");
        }

        $params['name'] = $name;
        $params['host'] = $host;

        $this->_config['services'][] = $params;

        return $params;
    }

    public function updateService($name, array $params)
    {
        $service = $this->findServiceByName($name);

        if (!$service) {
            throw new InvalidParamException("The service '$name' does not exists");
        }

        $services = $this->findServices();

        foreach ($services as $key => $service) {
            if ($service['name'] == $name) {
                $services[$key] = array_merge($service, $params);
                break;
            }
        }

        $this->_config['services'] = $services;

        return $services[$key];
    }

    public function deleteService($name)
    {
        $services = $this->findServices();

        foreach ($services as $key => $service) {
            if ($service['name'] == $name) {
                unset($services[$key]);
                break;
            }
        }

        $this->_config['services'] = $services;
    }

    public function addNode($serviceName, $nodeName, $def)
    {

    }

    public function updateNode($serviceName, $nodeName, $def)
    {

    }

    public function deleteNode($serviceName, $nodeName)
    {

    }

    public function persist()
    {
        file_put_contents(app()->runtime . '/config.json', Json::encode($this->_config));
    }
}