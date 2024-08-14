<?php

namespace App\Tests\Functional;

use ApiPlatform\Symfony\Bundle\Test\Client;
use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;


class CompanyTest extends ApiTestCase
{
    private ?string $token = null;

    public function setUp(): void
    {
        self::bootKernel();

        $client = static::createClient()->request('POST', '/api/login', [
            'json' => [
                'username' => 'user@test.com',
                'password' => 'password',
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $data = $client->toArray();
        $this->token = $data['token'];

        var_dump($this->token);
    }

    public function testCreateCompanyBySiren(): void
    {
        $response = static::createClient()->request('POST', '/api/companies', [
            'json' => [
                'siren' => 502704075,
            ],
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
            ],
        ]);

        $this->assertResponseIsSuccessful();

        $data = $response->toArray();
        self::assertArrayHasKey('id', $data);
    }

    public function testCreateCompanyBySirenNotOK(): void
    {
        static::createClient()->request('POST', '/api/companies', [
            'json' => [
                'siren' => 123456789,
            ],
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
            ],
        ]);

        self::assertResponseStatusCodeSame(400);
        self::assertJsonContains([
            'error' => 'Company not found',
        ]);
    }

    public function testCreateCompanyBySiretOK(): void
    {
        $response = static::createClient()->request('POST', '/api/companies', [
            'json' => [
                'siret' => 46920141200027,
            ],
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
            ],
        ]);

        $this->assertResponseIsSuccessful();

        $data = $response->toArray();
        self::assertArrayHasKey('id', $data);
    }

    public function testCreateCompanyBySiretNotOK(): void
    {
        static::createClient()->request('POST', '/api/companies', [
            'json' => [
                'siret' => 12345678900027,
            ],
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
            ],
        ]);

        self::assertResponseStatusCodeSame(400);
        self::assertJsonContains([
            'error' => 'Company not found',
        ]);
    }

    public function tearDown(): void
    {
        $client = static::createClient();

        $response = $client->request('GET', '/api/companies', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
            ],
        ]);

        $data = $response->toArray();

        foreach ($data as $company) {
            $client->request('DELETE', '/api/companies/'.$company['id'], [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->token,
                ],
            ]);
        }
    }
}
