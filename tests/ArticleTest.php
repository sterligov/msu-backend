<?php

namespace App\Tests;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * @group functional
 * Class ArticleTest
 * @package App\Tests
 */
class ArticleTest extends BaseApiFunctional
{
    private string $url;

    protected function setUp(): void
    {
        $this->url = '/api/articles';
    }

    public function testGetCollection()
    {
        $options = [
            'headers' => [
                'Accept' => 'application/ld+json'
            ]
        ];

        $response = static::createClient()->request(Request::METHOD_GET, $this->url, $options);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHasHeader('content-type', 'application/ld+json');
        $this->assertJsonContains([
            "@context" => "/api/contexts/Article",
            "@id" => "/api/articles",
            "@type" => "hydra:Collection",
//            "hydra:totalItems" => 110,
            "hydra:view" => [
                "@id" => "/api/articles?page=1",
                "@type" => "hydra:PartialCollectionView",
                "hydra:first" => "/api/articles?page=1",
//                "hydra:last" => "/api/articles?page=11",
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

        $this->assertErrorAccessWithoutRules(Request::METHOD_POST, $this->url, $options);
    }

    public function testDelete_withoutRules()
    {
        $this->assertErrorAccessWithoutRules(Request::METHOD_DELETE, "{$this->url}/1");
    }

    public function testUpdate_withoutRules()
    {
        $options = [
            'json' => [
                'title' => 'Test title',
            ],
            'headers' => [
                'Accept' => 'application/ld+json',
                'Content-Type' => 'application/json'
            ],
        ];

        $this->assertErrorAccessWithoutRules(Request::METHOD_PUT, "{$this->url}/1", $options);
    }

    public function testCreateArticle()
    {
        $options = [
            'json' => [
                'title' => 'My unique title ' . rand(0, 100000),
                'text' => 'Article text',
                'previewText' => 'Preview text',
                'mediaObjects' => ['/api/media_objects/1', '/api/media_objects/2'],
                'tags' => ['api/tags/Новости']
            ],
            'headers' => [
                'Accept' => 'application/ld+json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->auth()
            ],
        ];

        $response = static::createClient()->request(Request::METHOD_POST, $this->url, $options);

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        return $response->toArray();
    }

    /**
     * @depends testCreateArticle
     */
    public function testUpdate(array $article)
    {
        $options = [
            'json' => [
                'text' => 'Updated article text',
                'mediaObjects' => ['/api/media_objects/1'],
                'tags' => ['api/tags/Новости', 'api/tags/Объявления']
            ],
            'headers' => [
                'Accept' => 'application/ld+json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->auth()
            ],
        ];

        $response = static::createClient()->request(Request::METHOD_PUT, $article['@id'], $options);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        return $response->toArray();
    }

    /**
     * @depends testUpdate
     */
    public function testGetArticle(array $article)
    {
        $options = [
            'headers' => [
                'Accept' => 'application/ld+json',
            ],
        ];

        $response = static::createClient()->request(Request::METHOD_GET, $article['@id'], $options);

        $this->assertEquals($article, $response->toArray());

        return $article['@id'];
    }

    /**
     * @depends testGetArticle
     */
    public function testDeleteArticle(string $url)
    {
        $options = [
            'headers' => [
                'Accept' => 'application/ld+json',
                'Authorization' => 'Bearer ' . $this->auth()
            ],
        ];

        static::createClient()->request(Request::METHOD_DELETE, $url, $options);

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);

        static::createClient()->request(Request::METHOD_GET, $url, $options);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}