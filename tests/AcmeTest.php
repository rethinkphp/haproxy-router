<?php

namespace rethink\hrouter\tests;

use AcmePhp\Core\AcmeClient;
use AcmePhp\Ssl\KeyPair;
use rethink\hrouter\models\Challenge;
use rethink\hrouter\models\Domain;

/**
 * Class AcmeTest
 *
 * @package rethink\hrouter\tests
 */
class AcmeTest extends TestCase
{
    public function testGetAccountKeyPair()
    {
        $this->assertFalse(acme()->hasAccountKeyPair());

        $pair = acme()->getAccountKeyPair();

        $this->assertInstanceOf(KeyPair::class, $pair);
        $this->assertTrue(acme()->hasAccountKeyPair());

        $pair = acme()->getAccountKeyPair();
        $this->assertInstanceOf(KeyPair::class, $pair);
    }

    public function testGetClient()
    {
        $client = acme()->getClient();

        $this->assertInstanceOf(AcmeClient::class, $client);
    }

    public function testRegisterAccount()
    {
        $result = acme()->registerAccount();

        $this->assertTrue($result);
    }

    public function testRequestAuthorization()
    {
        acme()->registerAccount();

        $domain = new Domain(['name' => 'test.rethinkphp.me']);
        $challenge = acme()->requestAuthorization($domain);
        $this->assertInstanceOf(Challenge::class, $challenge);

        $this->assertTrue(Challenge::query()->where('domain', $domain->name)->exists());
    }
}