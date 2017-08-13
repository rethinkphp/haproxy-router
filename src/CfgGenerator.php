<?php

namespace rethink\hrouter;

use AcmePhp\Ssl\KeyPair;
use blink\core\Object;

use blink\support\Json;
use function normalize_path;
use rethink\hrouter\entities\RouteEntity;
use rethink\hrouter\models\Domain;

/**
 * Class CfgGenerator
 *
 * @package rethink\hrouter\services
 */
class CfgGenerator extends Object
{
    public $template = __DIR__ . '/templates/haproxy.cfg.php';
    /**
     * @var Haproxy
     */
    public $haproxy;

    protected $settings = [
        'username' => 'admin',
        'password' => 'haproxy-router',
    ];

    public function init()
    {
        $this->settings = array_merge($this->settings, settings()->all());
        if (!$this->haproxy) {
            $this->haproxy = haproxy();
        }

        $certsPath = $this->certsPath();
        if (!file_exists($certsPath)) {
           mkdir($certsPath);
        }
    }

    public function routeMap()
    {
        return normalize_path($this->haproxy->configDir . '/routes.map');
    }

    public function httpsMap()
    {
        return normalize_path($this->haproxy->configDir . '/tls-hosts.map');
    }

    public function certsPath()
    {
        return normalize_path($this->haproxy->configDir . '/certs');
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

    private $_domains;

    /**
     * @return Domain[]
     */
    public function getDomains()
    {
        if ($this->_domains === null) {
            $this->_domains = domains()->queryAll();
        }

        return $this->_domains;
    }


    public function hasCertificates()
    {
        foreach ($this->getDomains() as $domain) {
            if ($domain->hasCertificate()) {
                return true;
            }
        }

        return false;
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

    public function generateTlsHosts()
    {
        $results = [];

        foreach ($this->getDomains() as $domain) {
            if ($domain->tls_only) {
               $results[] = $domain->name;
            }
        }

        return implode("\n", $results);
    }

    protected function generateCertificate(Domain $domain)
    {
        if ($domain->tls_provider != Domain::TLS_PROVIDER_MANUAL) {
            $keyPair = $domain->getKeyPair();
            list($certPem, $chainPem) = Json::decode($domain->certificate);
            $certificate = $certPem . $chainPem . $keyPair->getPrivateKey()->getPEM();
        } else {
            $certificate = $domain->certificate2;
        }

        return $certificate;
    }

    /**
     * @return array
     */
    public function generateCertificates()
    {
        $certificates = [];

        foreach ($this->getDomains() as $domain) {
            if (!$domain->hasCertificate()) {
                continue;
            }

            $certificates[$domain->name . '.pem'] = $this->generateCertificate($domain);
        }

        return array_unique($certificates);
    }

    /**
     * @return array
     */
    public function generate()
    {
        ob_start();

        require $this->template;

        $conf = ob_get_clean();

        $files = [
            'haproxy.cfg' => $conf,
            'routes.map' => $this->generateRoutes($this->extractRoutes()),
            'tls-hosts.map' => $this->generateTlsHosts(),
        ];

        foreach ($this->generateCertificates() as $name => $certificate) {
            $files['certs/' . $name] = $certificate;
        }

        return $files;
    }
}
