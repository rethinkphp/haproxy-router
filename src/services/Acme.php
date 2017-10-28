<?php

namespace rethink\hrouter\services;


use AcmePhp\Core\AcmeClient;
use AcmePhp\Core\Exception\Protocol\ChallengeNotSupportedException;
use AcmePhp\Core\Exception\Protocol\ProtocolException;
use AcmePhp\Core\Exception\Server\MalformedServerException;
use AcmePhp\Core\Http\Base64SafeEncoder;
use AcmePhp\Core\Http\SecureHttpClient;
use AcmePhp\Core\Http\ServerErrorHandler;
use AcmePhp\Core\Protocol\AuthorizationChallenge;
use AcmePhp\Ssl\Certificate;
use AcmePhp\Ssl\CertificateRequest;
use AcmePhp\Ssl\CertificateResponse;
use AcmePhp\Ssl\DistinguishedName;
use AcmePhp\Ssl\Generator\KeyPairGenerator;
use AcmePhp\Ssl\KeyPair;
use AcmePhp\Ssl\ParsedCertificate;
use AcmePhp\Ssl\Parser\CertificateParser;
use AcmePhp\Ssl\Parser\KeyParser;
use AcmePhp\Ssl\PrivateKey;
use AcmePhp\Ssl\PublicKey;
use AcmePhp\Ssl\Signer\DataSigner;
use blink\core\InvalidParamException;
use blink\core\Object;
use blink\support\Json;
use GuzzleHttp\Client;
use rethink\hrouter\models\Challenge;
use rethink\hrouter\models\Domain;

/**
 * Class Acme
 *
 * @package rethink\hrouter\services
 */
class Acme extends Object
{
    const SETTING_ACCOUNT_KEY_PAIR   = 'acme.account_key_pair';
    const SETTING_ACCOUNT_REGISTERED = 'acme.account_registered';

    public $email;
    public $agreement;

    public $directoryUrl;

    public $defaultDistNames = [
        'C' => 'CN',
        'ST' => 'Beijing',
        'L' => 'Beijing',
        'O' => 'RethinkPHP Community',
        'OU' => 'IT',
    ];

    /**
     * @return bool
     */
    public function hasAccountKeyPair()
    {
        return settings()->has(self::SETTING_ACCOUNT_KEY_PAIR);
    }

    /**
     * @return KeyPair
     */
    public function getAccountKeyPair(): KeyPair
    {
        $pair = settings()->get(self::SETTING_ACCOUNT_KEY_PAIR);
        if (!$pair) {
            $pair = $this->generateAccountKeyPair();
        } else {
            $pair = $this->deserializeKeyPair($pair);
        }

        return $pair;
    }

    protected function serializeKeyPair(KeyPair $keyPair): string
    {
        return Json::encode([
            $keyPair->getPublicKey()->getPEM(),
            $keyPair->getPrivateKey()->getPEM(),
        ]);
    }

    public function deserializeKeyPair(string $pair): KeyPair
    {
        $pair = Json::decode($pair);

        return new KeyPair(
            new PublicKey($pair[0]),
            new PrivateKey($pair[1])
        );
    }

    protected function generateAccountKeyPair(): KeyPair
    {
        $pair = (new KeyPairGenerator())->generateKeyPair();

        settings()->set(self::SETTING_ACCOUNT_KEY_PAIR, $this->serializeKeyPair($pair));

        return $pair;
    }

    protected function isAccountRegistered()
    {
        return settings()->get(self::SETTING_ACCOUNT_REGISTERED, false);
    }

    protected function markAccountRegistered()
    {
        settings()->set(self::SETTING_ACCOUNT_REGISTERED, true);
    }

    /**
     * @return AcmeClient
     */
    public function getClient(): AcmeClient
    {
        $httpClient = new SecureHttpClient(
            $this->getAccountKeyPair(),
            new Client(),
            new Base64SafeEncoder(),
            new KeyParser(),
            new DataSigner(),
            new ServerErrorHandler()
        );

        return new AcmeClient($httpClient, $this->directoryUrl);
    }

    public function registerAccount()
    {
        $this->getClient()->registerAccount($this->agreement, $this->email);

        return true;
    }

    public function ensureAccountRegistered()
    {
        if ($this->isAccountRegistered()) {
            return;
        }

        try {
            $this->registerAccount();
            goto end;
        } catch (MalformedServerException $e) {
            if (strpos($e->getMessage(), 'Registration key is already in use') !== false) {
                goto end;
            }
            throw $e;
        }
end:
        $this->markAccountRegistered();
    }

    /**
     * @param Domain $domain
     * @return Challenge
     */
    public function requestAuthorization(Domain $domain): Challenge
    {
        /** @var \AcmePhp\Core\Protocol\AuthorizationChallenge[] $challenges */
        $challenges = $this->getClient()->requestAuthorization($domain->name);
        $theChallenge = null;

        foreach ($challenges as $challenge) {
            if ($challenge->getType() == 'http-01') {
                $theChallenge = $challenge;
                break;
            }
        }

        if (!$theChallenge) {
            throw new ChallengeNotSupportedException();
        }

        return Challenge::query()
            ->updateOrCreate(
                ['domain' => $theChallenge->getDomain()],
                $theChallenge->toArray()
            );
    }

    public function challengeAuthorization(Domain $domain)
    {
        $challenge = challenges()->loadByDomain($domain->name);

        $theChallenge = AuthorizationChallenge::fromArray($challenge->toArray());

        try {
            $this->getClient()->challengeAuthorization($theChallenge);
            return true;
        } catch (ProtocolException $e) {
            throw $e;
        }
    }

    public function getDistinguishedName(Domain $domain)
    {
        $names = array_merge(['E' => $this->email], $this->defaultDistNames, (array)$domain->dist_names);

        $result = new DistinguishedName(
            $domain->name,
            $names['C'],
            $names['ST'],
            $names['L'],
            $names['O'],
            $names['OU'],
            $names['E']
            // TODO alternativeNames
        );

        return $result;
    }

    protected function getOrCreateDomainKeyPair(Domain $domain)
    {
        if (!$domain->key_pair) {
            $pair = (new KeyPairGenerator())->generateKeyPair();
            $domain->key_pair = $this->serializeKeyPair($pair);
            $domain->save();
        } else {
            $pair = $this->deserializeKeyPair($domain->key_pair);
        }

        return $pair;
    }

    public function requestCertificate(Domain $domain)
    {
        if ($domain->tls_provider != Domain::TLS_PROVIDER_ACME) {
            throw new InvalidParamException(sprintf(
                'The domain: "%s" is not managed by acme',
                $domain->name
            ));
        }

        $distinguishedName = $this->getDistinguishedName($domain);

        $keyPair = $this->getOrCreateDomainKeyPair($domain);

        $csr = new CertificateRequest($distinguishedName, $keyPair);

        /** @var CertificateResponse $a */
        $response = $this->getClient()->requestCertificate($domain->name, $csr);

        $domain->certificate = $this->serializeCertificate($response->getCertificate());
        $domain->save();

        haproxy()->reloadAsync(true);

        return true;
    }

    protected function serializeCertificate(Certificate $certificate)
    {
        $certPem = $certificate->getPEM();

        $chainPems = array_map(function (Certificate $certificate) {
            return $certificate->getPEM();
        }, $certificate->getIssuerChain());

        return Json::encode([
            $certPem,
            implode("\n", $chainPems),
        ]);
    }

    public function refreshCertificatesIfNeeded()
    {
        $domains = domains()->queryAll();

        foreach ($domains as $domain) {
            if (!$domain->isAcme()) {
                continue;
            }

            $this->refreshCertificate($domain);
        }
    }

    protected function refreshCertificate(Domain $domain)
    {
        try {
            if (!$domain->isCertificateRequested()) {
                $this->handleCertificateRequest($domain);
            } else {
                $this->handleCertificateRenewIfNeeded($domain);
            }
        } catch (\Throwable $e) {
            app()->errorHandler->handleException($e);
        }
    }

    protected function handleCertificateRequest(Domain $domain)
    {
        $this->ensureAccountRegistered();

        logger()->info('request authorization for ' . $domain->name);
        $this->requestAuthorization($domain);

        logger()->info('challenge authorization for ' . $domain->name);
        $this->challengeAuthorization($domain);

        logger()->info('request certificate for ' . $domain->name);
        $this->requestCertificate($domain);
    }

    public function handleCertificateRenewIfNeeded(Domain $domain)
    {
        $certificate = new Certificate(Json::decode($domain->certificate)[0]);

        /** @var ParsedCertificate $parsed */
        $parsed = (new CertificateParser())->parse($certificate);

        if ($parsed->getValidTo()->getTimestamp() - time() >= 604800) {
            return;
        }

        logger()->info(sprintf(
            'Current certificate will expire in less than a week (%s), renewal is required.',
            $parsed->getValidTo()->format('Y-m-d H:i:s'))
        );

        logger()->info('renew certificate for ' . $domain->name);
        return $this->requestCertificate($domain);
    }
}