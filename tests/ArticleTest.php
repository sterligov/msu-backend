<?php

namespace App\Tests;


use Symfony\Component\HttpFoundation\Request;


/**
 * @group functional
 * Class ArticleTest
 * @package App\Tests
 */
class ArticleTest extends BaseApiFunctional
{
    public function testGetCollection()
    {
        $options = [
            'headers' => [
                'Accept' => 'application/ld+json'
            ]
        ];

        static::createClient()->request(Request::METHOD_GET, '/api/articles', $options);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHasHeader('content-type', 'application/ld+json');
        $this->assertJsonContains([
            "@context" => "/api/contexts/Article",
            "@id" => "/api/articles",
            "@type" => "hydra:Collection",
            "hydra:totalItems" => 110,
            "hydra:view" => [
                "@id" => "/api/articles?page=1",
                "@type" => "hydra:PartialCollectionView",
                "hydra:first" => "/api/articles?page=1",
                "hydra:last" => "/api/articles?page=11",
                "hydra:next" => "/api/articles?page=2"
            ],
        ]);
    }

    public function testCreate_withoutRules()
    {
        $options = [
            'json' => [
                'title' => 'Test title',
                'text' => 'Test text',
                'previewText' => 'Preview text',
            ],
            'headers' => [
                'Accept' => 'application/ld+json'
            ],
        ];

        $this->assertErrorAccessWithoutRules(Request::METHOD_POST, '/api/articles', $options);
    }

    public function testDelete_withoutRules()
    {
        $this->assertErrorAccessWithoutRules(Request::METHOD_DELETE, '/api/articles/1');
    }

    public function testUpdate_withoutRules()
    {
        $options = [
            'json' => [
                'title' => 'Test title',
            ],
            'headers' => [
                'Accept' => 'application/ld+json'
            ],
        ];

        $this->assertErrorAccessWithoutRules(Request::METHOD_PUT, '/api/articles/1', $options);
    }
}