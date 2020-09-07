<?php


namespace App\Services\Cache;


use App\Entity\Article;
use App\Entity\PreviewImage;
use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HTTPCache
{
    private TagAwareAdapterInterface $cache;

    public function __construct(TagAwareAdapterInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @throws \Psr\Cache\CacheException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function process(Request $request, Response $response)
    {
        $isArticle = $request->attributes->get('_api_resource_class') === Article::class;
        $isOKArticle = $isArticle && $response->getStatusCode() === Response::HTTP_OK;
  
        $tags = $this->getTags($request);
        $key = md5($request->getRequestUri());

        if (
            in_array($request->getMethod(), [Request::METHOD_GET, Request::METHOD_PUT])
            && $isOKArticle
        ) {
            $responseItem = $this->cache->getItem($key);
            $responseItem
                ->expiresAfter(60 * 60 * 24 * 3) // 3 days
                ->tag($tags)
                ->set($response->getContent());
            $this->cache->save($responseItem);
        } elseif ($request->getMethod() === Request::METHOD_DELETE) {
            $this->cache->deleteItem($key);
        }

        if ($request->getMethod() !== Request::METHOD_GET && $isArticle) {
            $this->cache->invalidateTags(['tags']);
        }
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function get(Request $request)
    {
        $cacheKey = md5($request->getRequestUri());

        return $this->cache->getItem($cacheKey)->get();
    }

    private function getTags(Request $request): array
    {
        $tags = [];
        if ($request->attributes->get('_route') === 'api_tags_articles_get_subresource') {
            $tags[] = 'tags';
        }

        return $tags;
    }
}