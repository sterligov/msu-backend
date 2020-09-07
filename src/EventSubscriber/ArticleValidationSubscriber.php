<?php


namespace App\EventSubscriber;


use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\Article;
use App\Exception\ArticleForbiddenException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\Security\Core\Security;

class ArticleValidationSubscriber implements EventSubscriberInterface
{
    /**
     * @var Security
     */
    private $security;

    /**
     * ArticleValidationSubscriber constructor.
     * @param Security $security
     */
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['checkArticleAvailability', EventPriorities::PRE_VALIDATE],
        ];
    }

    /**
     * @param ViewEvent $event
     * @throws ArticleForbiddenException
     */
    public function checkArticleAvailability(ViewEvent $event): void
    {
        $article = $event->getControllerResult();

        if ($article instanceof Article) {
            foreach ($article->getTags() as $tag) {
                if ($tag->getName() === 'Закрытая страница') {
                    $event->getRequest()->attributes->set('is_need_caching', false);
                    if (!$this->security->isGranted('ROLE_USER')) {
                        throw new ArticleForbiddenException('Access denied');
                    }
                }
            }
        }
    }
}