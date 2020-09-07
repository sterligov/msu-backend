<?php

namespace App\EventListener;

use App\Services\Cache\HTTPCache;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

final class CacheRequestResponseListener
{
    private ?HTTPCache $cache = null;

    public function __construct(HTTPCache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @param ResponseEvent $event
     * @throws \Psr\Cache\CacheException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function onKernelResponse(ResponseEvent $event)
    {
        $response = $event->getResponse();
        $request = $event->getRequest();

        if ($request->get('is_need_caching', true) === true) {
            $this->cache->process($request, $response);
        }
    }

    /**
     * @param RequestEvent $event
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();

        if ($request->getMethod() === Request::METHOD_GET) {
            $cachedValue = $this->cache->get($request);

            if (!empty($cachedValue)) {
                $request->attributes->set('is_need_caching', false);
                $event->setResponse(new Response($cachedValue));
            }
        }
    }
}