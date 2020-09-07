<?php


namespace App\Tests;


use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;


class BaseApiFunctional extends ApiTestCase
{
    protected function assertErrorAccessWithoutRules($method, $url, $options = [])
    {
        static::createClient()->request($method, $url, $options);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }
}