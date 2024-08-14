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
    }

    protected function createClientWithCredentials($token = null): Client
    {
        $token = $token ?: $this->getToken();

        return static::createClient([], ['headers' => ['authorization' => 'Bearer '.$token]]);
    }
    
    protected function getToken($body = []): string
    {
        if ($this->token) {
            return $this->token;
        }

        $response = static::createClient()->request('POST', '/api/login', ['json' => $body ?: [
            'username' => 'user@test.com',
            'password' => 'password',
        ]]);

        $this->assertResponseIsSuccessful();
        $data = $response->toArray();
        $this->token = $data['token'];

        return $data['token'];
    }

    public function testCreateCompanyBySiren(): void
    {
        $token = $this->getToken();

        $response = static::createClient()->request('POST', '/api/companies', [
            'json' => [
                'siren' => 502704075,
            ],
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        $this->assertResponseIsSuccessful();

        $data = $response->toArray();
        self::assertArrayHasKey('id', $data);
    }

    public function testCreateCompanyBySirenNotOK(): void
    {
        $token = $this->getToken();

        $response = static::createClient()->request('POST', '/api/companies', [
            'json' => [
                'siren' => 123456789,
            ],
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        self::assertResponseStatusCodeSame(400);
        self::assertJsonContains([
            'error' => 'Company not found',
        ]);
    }

    public function testCreateCompanyBySiretOK(): void
    {
        $token = $this->getToken();

        $response = static::createClient()->request('POST', '/api/companies', [
            'json' => [
                'siret' => 46920141200027,
            ],
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        $this->assertResponseIsSuccessful();

        $data = $response->toArray();
        self::assertArrayHasKey('id', $data);
    }

    public function testCreateCompanyBySiretNotOK(): void
    {
        $token = $this->getToken();

        $response = static::createClient()->request('POST', '/api/companies', [
            'json' => [
                'siret' => 12345678900027,
            ],
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        self::assertResponseStatusCodeSame(400);
        self::assertJsonContains([
            'error' => 'Company not found',
        ]);
    }

    public function tearDown(): void
    {
        $token = $this->getToken();

        $client = $this->createClientWithCredentials($token);

        $response = $client->request('GET', '/api/companies');

        $data = $response->toArray();

        foreach ($data as $company) {
            $client->request('DELETE', '/api/companies/'.$company['id']);
        }
    }
}
