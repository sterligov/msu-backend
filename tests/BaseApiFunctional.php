<?php


namespace App\Tests;


use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class BaseApiFunctional extends ApiTestCase
{
    protected function assertErrorAccessWithoutRules($method, $url, $options = [])
    {
        static::createClient()->request($method, $url, $options);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    protected function auth()
    {
        $options = [
            'json' => [
                'username' => 'msu',
                'password' => 'msu_pass',
            ],
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ],
        ];

        $response = static::createClient()->request(Request::METHOD_POST, '/api/login', $options);

        return $response->toArray()['token'];
    }
}