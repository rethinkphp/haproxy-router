<?php

namespace rethink\hrouter;

use blink\core\Object;

use function normalize_path;

/**
 * Class CfgGenerator
 *
 * @package rethink\hrouter\services
 */
class CfgGenerator extends Object
{
    public $template = __DIR__ . '/templates/haproxy.cfg.php';
    public $configDir;
    public $httpPort = 80;
    public $httpsPort = 443;

    /**
     * @param array $config
     *  + services
     *      - name
     *      - host
     *      - rewrites
     *      - health check
     *      - routes
     *      - nodes (aka. servers)
     *  + ...
     */
    public $services = [];

    public function routeMap()
    {
        return normalize_path($this->configDir . '/routes.map');
    }

    public function httpsMap()
    {
        return normalize_path($this->configDir . '/https-hosts.map');
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
     * @return array
     */
    public function generate()
    {
        ob_start();

        require $this->template;

        $conf = ob_get_clean();

        return [
            'haproxy.cfg' => $conf,
            'routes.map' => '',
            'https-hosts.map' => '',
        ];
    }
}
