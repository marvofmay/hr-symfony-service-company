<?php

namespace App\Module\Company\Application\EventSubscriber\Role;

use App\Module\Company\Application\Event\Role\RoleAssignedAccessesEvent;
use App\Module\Company\Application\Event\Role\RoleAssignedPermissionsEvent;
use App\Module\Company\Application\Event\Role\RoleDeletedEvent;
use App\Module\Company\Application\Event\Role\RoleImportedEvent;
use App\Module\Company\Application\Event\Role\RoleListedEvent;
use App\Module\Company\Application\Event\Role\RoleMultipleDeletedEvent;
use App\Module\Company\Application\Event\Role\RoleUpdatedEvent;
use App\Module\Company\Application\Event\Role\RoleViewedEvent;
use App\Module\Company\Domain\Entity\Role;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Module\Company\Application\Event\Role\RoleCreatedEvent;
use Symfony\Component\Serializer\SerializerInterface;

final readonly class RoleEventLoggerSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            RoleCreatedEvent::class => 'onCreated',
            RoleUpdatedEvent::class => 'onUpdated',
            RoleDeletedEvent::class => 'onDeleted',
            RoleViewedEvent::class => 'onViewed',
            RoleListedEvent::class => 'onListed',
            RoleImportedEvent::class => 'onImported',
            RoleAssignedAccessesEvent::class => 'onAssignedAccesses',
            RoleAssignedPermissionsEvent::class => 'onAssignedPermissions',
            RoleMultipleDeletedEvent::class => 'onMultipleDeleted',
        ];
    }

    public function __construct(
        private EntityManagerInterface $em,
        private SerializerInterface $serializer,
        private LoggerInterface $logger
    )
    {}

    public function onCreated(RoleCreatedEvent $event): void
    {
        $this->log($event::class, Role::class, $this->serializer->serialize($event->role, 'json'));
    }

    public function onUpdated(RoleUpdatedEvent $event): void
    {
        $this->log($event::class, Role::class, $this->serializer->serialize($event->role, 'json'));
    }

    public function onDeleted(RoleDeletedEvent $event): void
    {
        $this->log($event::class, Role::class, $this->serializer->serialize($event->role, 'json'));
    }

    public function onViewed(RoleViewedEvent $event): void
    {
        $this->log($event::class, Role::class, $this->serializer->serialize($event->data, 'json'));
    }

    public function onListed(RoleListedEvent $event): void
    {
        $this->log($event::class, Role::class, $this->serializer->serialize($event->data, 'json'));
    }

    public function onImported(RoleImportedEvent $event): void
    {
        $this->log($event::class, Role::class, $this->serializer->serialize($event->roles, 'json'));
    }

    public function onAssignedAccesses(RoleAssignedAccessesEvent $event): void
    {
        $this->log(
            $event::class,
            Role::class,
            $this->serializer->serialize($event->role, 'json', [
                'circular_reference_handler' => function ($object) {
                    return $object->getUUID();
                },
            ])
        );
    }

    public function onAssignedPermissions(RoleAssignedPermissionsEvent $event): void
    {
        $this->log(
            $event::class,
            Role::class,
            $this->serializer->serialize($event->getData(), 'json', [
                'circular_reference_handler' => function ($object) {
                    return $object->getUUID();
                },
            ])
        );
    }

    public function onMultipleDeleted(RoleMultipleDeletedEvent $event): void
    {
        $this->log($event::class, Role::class, $this->serializer->serialize($event->roles, 'json'));
    }


    private function log(string $event, string $entity, string $data): void
    {
        $this->logger->info('-------------------------------------------------------------');
        $this->logger->info(sprintf('event: %s ', $event));
        $this->logger->info(sprintf('entity: %s ', $entity));
        $this->logger->info(sprintf('data: %s ', $data));
        $this->logger->info('-------------------------------------------------------------');

        ///$this->em->persist(new EventLog($event, $entity, $data));
        //$this->em->flush();
    }
}
