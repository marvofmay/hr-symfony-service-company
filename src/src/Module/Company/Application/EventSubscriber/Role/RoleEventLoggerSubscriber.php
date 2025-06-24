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
use App\Module\Company\Domain\Entity\Employee;
use App\Module\Company\Domain\Entity\Role;
use App\Module\System\Domain\Entity\EventLog;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
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
        private LoggerInterface $logger,
        private Security $security,
    )
    {
    }

    public function onCreated(RoleCreatedEvent $event): void
    {
        $this->log($event::class, Role::class, $this->serializer->serialize($event->getData(), 'json'));
    }

    public function onUpdated(RoleUpdatedEvent $event): void
    {
        $this->log($event::class, Role::class, $this->serializer->serialize($event->getData(), 'json'));
    }

    public function onDeleted(RoleDeletedEvent $event): void
    {
        $this->log($event::class, Role::class, $this->serializer->serialize($event->uuid, 'json'));
    }

    public function onViewed(RoleViewedEvent $event): void
    {
        $this->log($event::class, Role::class, $this->serializer->serialize($event->uuid, 'json'));
    }

    public function onListed(RoleListedEvent $event): void
    {
        $this->log($event::class, Role::class, $this->serializer->serialize($event->query, 'json'));
    }

    public function onImported(RoleImportedEvent $event): void
    {
        $this->log($event::class, Role::class, $this->serializer->serialize($event->data, 'json'));
    }

    public function onAssignedAccesses(RoleAssignedAccessesEvent $event): void
    {
        $this->log($event::class, Role::class, $this->serializer->serialize($event->data, 'json'));
    }

    public function onAssignedPermissions(RoleAssignedPermissionsEvent $event): void
    {
        $this->log($event::class, Role::class, $this->serializer->serialize($event->data, 'json'));
    }

    public function onMultipleDeleted(RoleMultipleDeletedEvent $event): void
    {
        $this->log($event::class, Role::class, $this->serializer->serialize($event->data, 'json'));
    }


    private function log(string $event, string $entity, string $data): void
    {
        $user = $this->security->getUser();
        $employee = $user->getEmployee();

        $this->logger->info('-------------------------------------------------------------');
        $this->logger->info(sprintf('event: %s', $event));
        $this->logger->info(sprintf('entity: %s', $entity));
        $this->logger->info(sprintf('data: %s', $data));
        $this->logger->info(
            sprintf($employee ? 'employeeUUID: %s ' : 'userUUID: %s ', $employee ? $employee->getUUID() : $user->getUUID())
        );
        $this->logger->info('-------------------------------------------------------------');


        $this->em->persist(new EventLog($event, $entity, $data, $employee));
        $this->em->flush();
    }
}
