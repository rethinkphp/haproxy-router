<?php

namespace rethink\hrouter\tests;

use AcmePhp\Core\AcmeClient;
use AcmePhp\Ssl\KeyPair;

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
}