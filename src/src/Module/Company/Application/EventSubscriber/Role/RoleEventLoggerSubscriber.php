<?php

namespace App\Module\Company\Application\EventSubscriber\Role;

use App\Module\Company\Domain\Entity\Role;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Module\Company\Application\Event\Role\RoleCreatedEvent;
use Symfony\Component\Serializer\SerializerInterface;

final class RoleEventLoggerSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            RoleCreatedEvent::class => 'onCreated',
            //RoleUpdatedEvent::class => 'onUpdated',
            //RoleDeletedEvent::class => 'onDeleted',
            //RoleViewedEvent::class => 'onViewed',
            //RoleListEvent::class => 'onListed',
        ];
    }

    public function __construct(private readonly EntityManagerInterface $em, private SerializerInterface $serializer, private readonly LoggerInterface $logger) {}

    public function onCreated(RoleCreatedEvent $event): void
    {
        $this->log($event::class, Role::class, $this->serializer->serialize($event->role, 'json'));
    }

    //public function onUpdated(RoleUpdatedEvent $event): void
    //{
    //    $this->log('role_updated', $event->getRole()->getId());
    //}
    //
    //public function onDeleted(RoleDeletedEvent $event): void
    //{
    //    $this->log('role_deleted', $event->getRole()->getId());
    //}
    //
    //public function onViewed(RoleViewedEvent $event): void
    //{
    //    $this->log('role_viewed', $event->getRole()->getId());
    //}
    //
    //public function onListed(RoleListEvent $event): void
    //{
    //    $this->log('role_listed', null);
    //}

    private function log(string $event, string $entity, string $json): void
    {
        $this->logger->info($json);

        ///$this->em->persist(new EventLog($event, ['id' => $id]));
        //$this->em->flush();
    }
}
