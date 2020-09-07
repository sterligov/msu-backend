<?php

namespace App\Tests;

use \ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use \Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpFoundation\Response;

/**
 * Class LoginTest
 * @group functional
 */
class LoginTest extends ApiTestCase
{
    public function testLogin_success(): void
    {
        $options = [
            'json' => [
                'username' => 'msu',
                'password' => 'msu_pass'
            ],
            'headers' => [
                'Accept' => 'application/json'
            ],
        ];

        $response = static::createClient()->request(Request::METHOD_POST, '/api/login', $options);
        $content = $response->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');
        $this->assertArrayHasKey('token', $content);
        $this->assertArrayHasKey('roles', $content);
    }

    public function testLogin_fail(): void
    {
        $options = [
            'json' => [
                'username' => 'bad_username',
                'password' => 'bad_pass'
            ],
            'headers' => [
                'Accept' => 'application/json'
            ],
        ];

        static::createClient()->request(Request::METHOD_POST, '/api/login', $options);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }
}