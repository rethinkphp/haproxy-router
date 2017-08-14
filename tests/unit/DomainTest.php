<?php

namespace rethink\hrouter\tests\unit;

use rethink\hrouter\models\Domain;
use rethink\hrouter\support\ValidationException;
use rethink\hrouter\tests\TestCase;

/**
 * Class DomainTest
 *
 * @package rethink\hrouter\tests\unit
 */
class DomainTest extends TestCase
{
    public function certificates()
    {
        return [
            [
                Domain::TLS_PROVIDER_MANUAL,
                'invalid certificate',
                false,
                [
                    [
                        'field' => 'certificate2',
                        'message' => 'The supplied certificate is invalid',
                    ]
                ]
            ],
            [
                Domain::TLS_PROVIDER_MANUAL,
                '-----BEGIN CERTIFICATE-----
MIIFijCCBHKgAwIBAgITAP+qRDFWSe3TiEaVN0cCPHnwKjANBgkqhkiG9w0BAQsF
ADAfMR0wGwYDVQQDDBRoMnBweSBoMmNrZXIgZmFrZSBDQTAeFw0xNzA4MDUwNjA3
MDBaFw0xNzExMDMwNjA3MDBaMEoxGTAXBgNVBAMTEGd3LnJldGhpbmtwaHAubWUx
LTArBgNVBAUTJGZmYWE0NDMxNTY0OWVkZDM4ODQ2OTUzNzQ3MDIzYzc5ZjAyYTCC
AiIwDQYJKoZIhvcNAQEBBQADggIPADCCAgoCggIBAM3iDuB33/w2AMi2dWYZJhN6
Lz4YCmMr7GKVw5kLdrIBFWmQBTc10vnpztSak+XarfhqB0VYhhCVGkCbHfl8S1uo
plZItXyqo8YtaUwbBeUBkpkqWlcdQKt3k77gWT6VpZ1+P9Yz7VuaSM9pwcb/TyA/
G6uFZyTD3RMOksFfsnGiYx39HA/pK8U6Eor+2eE3XV+AbbmdyJJj3Roz7NxHiY4W
ZM4IdJ8EMGXQRvUXY+a3nJyoRXHxDNYMWdD1Kv60YeiS+w8yHHxS6ltJXtZtAB6w
LBQeFeiJczd+3NuoE1Oz5oXdTNi6IvYjClUlZ6/rc31H4ENSnox81/Oarp9qYKsn
9Gxa4VYWxSpk87bW7y9JsVy6XIdZKMSCj3oMFRC7zkrrTaLnkHyDwH3cmQOYh/gw
CiSIgg//Yam0RPUFC0PA0ZAe0xg8bIBtfnEvGNwkkjMUvCsETAVEyijGfzOlFqWr
DrPKYj6S37LpK+h+tRXqdngffljvJDaxkYdoH9bPYcTST/vwMqQUNNvU8IkvlJ98
NXv0tJSpRu0F6Gw3/tmJEPl0KdQN2cqcWtVMJbSw99f+XIUpzJc3R8e1OzSrMghf
IvHaiTpJKIAJv9Uz9X4YS442WTUqoJVwUuv7jYWgvCSzMYdsceYfOD8AnaOCuZS9
MDX5Ymy+9JA+P9PIXdDpAgMBAAGjggGSMIIBjjAOBgNVHQ8BAf8EBAMCBaAwHQYD
VR0lBBYwFAYIKwYBBQUHAwEGCCsGAQUFBwMCMAwGA1UdEwEB/wQCMAAwHQYDVR0O
BBYEFD4EnGfvcx96mZH8BFtQqxJiZY03MB8GA1UdIwQYMBaAFPt4TxL5YBWDLJ8X
fzQZsy426kGJMGYGCCsGAQUFBwEBBFowWDAiBggrBgEFBQcwAYYWaHR0cDovLzEy
Ny4wLjAuMTo0MDAyLzAyBggrBgEFBQcwAoYmaHR0cDovLzEyNy4wLjAuMTo0MDAw
L2FjbWUvaXNzdWVyLWNlcnQwGwYDVR0RBBQwEoIQZ3cucmV0aGlua3BocC5tZTAn
BgNVHR8EIDAeMBygGqAYhhZodHRwOi8vZXhhbXBsZS5jb20vY3JsMGEGA1UdIARa
MFgwCAYGZ4EMAQIBMEwGAyoDBDBFMCIGCCsGAQUFBwIBFhZodHRwOi8vZXhhbXBs
ZS5jb20vY3BzMB8GCCsGAQUFBwICMBMMEURvIFdoYXQgVGhvdSBXaWx0MA0GCSqG
SIb3DQEBCwUAA4IBAQC1D1SxZq24cOQUG5PUOFG2iEqaEaMOTMti/Fq39I1H/tyh
Hs0qiHfd7Ml07UFP+LgIFsy0DGzFFEcEFCs9GlbLpIoO+sbJXwwfkEbMUDkdIhxe
z8FZXiEoeWN6HHa3xS5dkxkmJ5HysFxJj86D4FkSZ2cgEIfOujXaxqacr68VHfo4
CUtV/bKpVPs3FsgI/mDS4sKXNCii9pIVd0HEq67QpPeyQuMC8wUjYxv67fMRBTgl
02VSgkgAZWaHqMUZNJHWm71/t3i95uOwuASsJyTwE2AaxwBxgPvwEvyHDNt1vwdF
UXsyuUMsWGRRLPLle/XakHu5Fwgtqa7cJKGhQRKC
-----END CERTIFICATE-----
',
                true,
                [],
            ],
            [
                Domain::TLS_PROVIDER_ACME,
                null,
                true,
                [],
            ]
        ];
    }

    /**
     * @dataProvider certificates
     */
    public function testValidateCertificate($provider, $certificate, $result, $errors)
    {
        $domain = domains()->create(['name' => 'foo.rethinkphp.com', 'tls_provider' => $provider]);

        try {
            domains()->update($domain, ['certificate2' => $certificate]);
            $this->assertTrue($result);
        } catch (ValidationException $e) {
            $this->assertEquals($errors, $e->errors);
        }
    }
}
