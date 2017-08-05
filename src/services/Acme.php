<?php

namespace rethink\hrouter\services;


use AcmePhp\Core\AcmeClient;
use AcmePhp\Core\Http\Base64SafeEncoder;
use AcmePhp\Core\Http\SecureHttpClient;
use AcmePhp\Core\Http\ServerErrorHandler;
use AcmePhp\Ssl\Generator\KeyPairGenerator;
use AcmePhp\Ssl\KeyPair;
use AcmePhp\Ssl\Parser\KeyParser;
use AcmePhp\Ssl\PrivateKey;
use AcmePhp\Ssl\PublicKey;
use AcmePhp\Ssl\Signer\DataSigner;
use blink\core\Object;
use blink\support\Json;
use GuzzleHttp\Client;

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
            $pair = $this->deserializeAccountKeyPair($pair);
        }

        return $pair;
    }

    protected function deserializeAccountKeyPair($pair): KeyPair
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

        settings()->set(self::SETTING_ACCOUNT_KEY_PAIR, Json::encode([
            $pair->getPublicKey()->getPEM(),
            $pair->getPrivateKey()->getPEM(),
        ]));

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
}