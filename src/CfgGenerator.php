<?php

namespace rethink\hrouter;

use blink\core\Object;

use function normalize_path;
use rethink\hrouter\entities\RouteEntity;

/**
 * Class CfgGenerator
 *
 * @package rethink\hrouter\services
 */
class CfgGenerator extends Object
{
    public $template = __DIR__ . '/templates/haproxy.cfg.php';
    public $configDir;

    protected $settings = [
        'username' => 'admin',
        'password' => 'haproxy-router',
    ];

    public function init()
    {
        $this->settings = array_merge($this->settings, settings()->all());
    }

    public function routeMap()
    {
        return normalize_path($this->configDir . '/routes.map');
    }

    public function httpsMap()
    {
        return normalize_path($this->configDir . '/https-hosts.map');
    }

    public function setting($name, $default = null)
    {
        return $this->settings[$name] ?? $default;
    }

    private $_services;

    public function getServices()
    {
        if ($this->_services === null) {
            $this->_services = services()->queryAll();
        }

        return $this->_services;
    }


    /**
     * Generate a server line
     *
     * @param array $definition The server definition
     *  + name
     *  + host
     *  + backup
     *  + disabled
     * @return string
     */
    public function generateServer($definition)
    {
        $segments = ['server', $definition['name'], $definition['host']];

        if ($definition['check'] ?? false) {
            $segments[] = 'check';
        }

        if ($definition['backup'] ?? false) {
            $segments[] = 'backup';
        }

        return implode(' ', $segments);
    }

    /**
     * @param array $routeMaps
     * @return string
     */
    public function generateRoutes(array $routeMaps)
    {
        $lines = [];

        foreach ($routeMaps as $service => $routes) {
            /** @var RouteEntity $route */
            foreach ($routes as $route) {
                $lines[] = "^{$route->host}{$route->path} service_$service";
            }
        }

        rsort($lines);

        return implode("\n", $lines);
    }

    protected function extractRoutes()
    {
        $routeMaps = [];

        foreach ($this->getServices() as $service) {
            $routeMaps[$service->name] = $service->routes;
        }

        return $routeMaps;
    }

    /**
     * @return array
     */
    public function generate()
    {
        ob_start();

        require $this->template;

        $conf = ob_get_clean();

        return [
            'haproxy.cfg' => $conf,
            'routes.map' => $this->generateRoutes($this->extractRoutes()),
            'https-hosts.map' => '',
        ];
    }
}
