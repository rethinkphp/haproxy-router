<?php

namespace rethink\hrouter;

use AcmePhp\Ssl\KeyPair;
use blink\core\Object;

use blink\support\Json;
use function normalize_path;
use rethink\hrouter\entities\RouteEntity;
use rethink\hrouter\models\Domain;
use rethink\hrouter\models\Service;

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

    protected $configDir;

    protected $settings = [];
    /**
     * @var Service[]
     */
    protected $services = [];

    /**
     * @var Domain[]
     */
    protected $domains = [];


    public function init()
    {
        $this->haproxy = $this->haproxy ?: haproxy();

        $this->settings = array_merge([
            'username' => $this->haproxy->username,
            'password' => $this->haproxy->password,
        ], settings()->all());

        $this->services = services()->queryAll();
        $this->domains  = domains()->queryAll();
    }

    public function routeMap()
    {
        return normalize_path($this->configDir . '/routes.map');
    }

    public function httpsMap()
    {
        return normalize_path($this->configDir . '/tls-hosts.map');
    }

    public function certsPath()
    {
        $path = get_existed_path($this->configDir . '/certs');

        return normalize_path($path);
    }

    public function setting($name, $default = null)
    {
        return $this->settings[$name] ?? $default;
    }

    public function hasCertificates()
    {
        foreach ($this->domains as $domain) {
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

        foreach ($this->services as $service) {
            $routeMaps[$service->name] = $service->routes;
        }

        return $routeMaps;
    }

    public function generateTlsHosts()
    {
        $results = [];

        foreach ($this->domains as $domain) {
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

        foreach ($this->domains as $domain) {
            if (!$domain->hasCertificate()) {
                continue;
            }

            $certificates[$domain->name . '.pem'] = $this->generateCertificate($domain);
        }

        return array_unique($certificates);
    }

    protected function removeInvalidCertificates($validCertificates)
    {
        $existedCertificates = glob($this->configDir . '/certs/*.pem');

        $outdatedCertificates = array_diff($existedCertificates, $validCertificates);

        if (empty($outdatedCertificates)) {
            return;
        }

        logger()->info('removing outdated certificates', $outdatedCertificates);

        array_map('unlink', $outdatedCertificates);
    }

    /**
     * Generate config file for HAProxy.
     *
     * @param string $configDir
     * @return string
     */
    public function generate($configDir = null)
    {
        $this->configDir = get_existed_path($configDir ?: $this->haproxy->configDir);

        ob_start();

        require $this->template;

        $conf = ob_get_clean();

        $files = [
            'haproxy.cfg' => $conf,
            'routes.map' => $this->generateRoutes($this->extractRoutes()),
            'tls-hosts.map' => $this->generateTlsHosts(),
        ];

        $validCertificates = [];

        foreach ($this->generateCertificates() as $name => $certificate) {
            $files['certs/' . $name] = $certificate;
            $validCertificates[] = $this->configDir . '/certs/' . $name;
        }

        foreach ($files as $name => $content) {
            $configFile = $configDir . '/' . $name;

            file_put_contents($configFile, $content);
        }

        $this->removeInvalidCertificates($validCertificates);

        return $this->configDir . '/haproxy.cfg';
    }
}
