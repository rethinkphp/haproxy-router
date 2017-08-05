<?php

namespace rethink\hrouter\services;


use AcmePhp\Core\AcmeClient;
use AcmePhp\Core\Exception\Protocol\ChallengeNotSupportedException;
use AcmePhp\Core\Exception\Protocol\ProtocolException;
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
    const SETTING_ACCOUNT_KEY_PAIR = 'acme.account_key_pair';

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

        if ($domain->certificate) {
            throw new InvalidParamException(sprintf(
                'The domain: "%s" is already has a certificate, requestCertificate() should be used instead',
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

        haproxy()->reload(true);

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
}