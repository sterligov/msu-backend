<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use ApiPlatform\Core\Util\RequestAttributesExtractor;
use App\Entity\MediaObject;
use App\Services\MediaObjectContentUrlResolver;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Vich\UploaderBundle\Storage\StorageInterface;

final class ResolveMediaObjectContentUrlSubscriber implements EventSubscriberInterface
{
    /**
     * @var MediaObjectContentUrlResolver
     */
    private $urlResolver;

    /**
     * ResolveMediaObjectContentUrlSubscriber constructor.
     * @param MediaObjectContentUrlResolver $urlResolver
     */
    public function __construct(MediaObjectContentUrlResolver $urlResolver)
    {
        $this->urlResolver = $urlResolver;
    }

    /**
     * @return array|array[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['onPreSerialize', EventPriorities::PRE_SERIALIZE],
        ];
    }

    /**
     * @param ViewEvent $event
     */
    public function onPreSerialize(ViewEvent $event): void
    {
        $controllerResult = $event->getControllerResult();
        $request = $event->getRequest();

        if (
            $controllerResult instanceof Response
            || !$request->attributes->getBoolean('_api_respond', true)
        ) {
            return;
        }

        if (
            !($attributes = RequestAttributesExtractor::extractAttributes($request))
            || !\is_a($attributes['resource_class'], MediaObject::class, true)
        ) {
            return;
        }

        $mediaObjects = $controllerResult;

        if (!is_iterable($mediaObjects)) {
            $mediaObjects = [$mediaObjects];
        }

        foreach ($mediaObjects as $mediaObject) {
            if (!$mediaObject instanceof MediaObject) {
                continue;
            }

            $mediaObject->setContentUrl($this->urlResolver->resolve($mediaObject));
        }
    }
}