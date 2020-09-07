<?php


namespace App\EventListener;

use App\Entity\Appeal;
use App\Message\UserAppeal;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Messenger\MessageBusInterface;

final class AppealListener
{
    private MessageBusInterface $bus;

    public function __construct(MessageBusInterface $bus)
    {
        $this->bus = $bus;
    }

    /**
     * @param Appeal $appeal
     * @param LifecycleEventArgs $event
     */
    public function postPersist(Appeal $appeal, LifecycleEventArgs $event)
    {
        $this->bus->dispatch(new UserAppeal($appeal->getId()));
    }
}