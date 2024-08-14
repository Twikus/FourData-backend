<?php

namespace App\Tests\Functional;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;

class SecurityTest extends ApiTestCase
{
    private ?string $token = null;

    public function testLoginOK(): void
    {
        $response = static::createClient()->request('GET', '/api/login', [
            'json' => [
                'username' => 'user@test.com',
                'password' => 'password',
            ],
        ]);

        $this->assertResponseIsSuccessful();
        
        $data = $response->toArray();
        self::assertArrayHasKey('token', $data);
    }

    public function testLoginNotOK(): void
    {
        static::createClient()->request('GET', '/api/login', [
            'json' => [
                'username' => 'email@email.com',
                'password' => 'paword',
            ],
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testRegistrationOK(): void
    {
        $response = static::createClient()->request('POST', '/api/register', [
            'json' => [
                'email' => 'test@valid.fr',
                'password' => 'password',
                'firstname' => 'John',
                'lastname' => 'Doe',
            ],
        ]);

        $this->assertResponseIsSuccessful();

        $data = $response->toArray();
        $this->token = $data['token'];

        self::assertArrayHasKey('token', $data);
    }

    public function testRegistrationNotOK(): void
    {
        static::createClient()->request('POST', '/api/register', [
            'json' => [
                'email' => 'testinvalid',
                'password' => 'password',
                'firstname' => 'John',
                'lastname' => 'Doe',
            ],
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }
}
