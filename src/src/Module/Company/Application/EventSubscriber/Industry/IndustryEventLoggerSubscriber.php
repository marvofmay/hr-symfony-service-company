<?php

namespace App\Module\Company\Application\EventSubscriber\Industry;

use App\Module\Company\Domain\Entity\Industry;
use App\Module\System\Domain\Entity\EventLog;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Module\Company\Application\Event\Industry\IndustryCreatedEvent;
use Symfony\Component\Serializer\SerializerInterface;

final readonly class IndustryEventLoggerSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            IndustryCreatedEvent::class => 'onCreated',
            //IndustryUpdatedEvent::class => 'onUpdated',
            //IndustryDeletedEvent::class => 'onDeleted',
            //IndustryViewedEvent::class => 'onViewed',
            //IndustryListedEvent::class => 'onListed',
            //IndustryImportedEvent::class => 'onImported',
            //IndustryAssignedAccessesEvent::class => 'onAssignedAccesses',
            //IndustryAssignedPermissionsEvent::class => 'onAssignedPermissions',
            //IndustryMultipleDeletedEvent::class => 'onMultipleDeleted',
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

    public function onCreated(IndustryCreatedEvent $event): void
    {
        $this->log($event::class, Industry::class, $this->serializer->serialize($event->getData(), 'json'));
    }

    //public function onUpdated(IndustryUpdatedEvent $event): void
    //{
    //    $this->log($event::class, Industry::class, $this->serializer->serialize($event->role, 'json'));
    //}
    //
    //public function onDeleted(IndustryDeletedEvent $event): void
    //{
    //    $this->log($event::class, Industry::class, $this->serializer->serialize($event->role, 'json'));
    //}
    //
    //public function onViewed(IndustryViewedEvent $event): void
    //{
    //    $this->log($event::class, Industry::class, $this->serializer->serialize($event->data, 'json'));
    //}
    //
    //public function onListed(IndustryListedEvent $event): void
    //{
    //    $this->log($event::class, Industry::class, $this->serializer->serialize($event->data, 'json'));
    //}
    //
    //public function onImported(IndustryImportedEvent $event): void
    //{
    //    $this->log($event::class, Industry::class, $this->serializer->serialize($event->roles, 'json'));
    //}
    //
    //public function onAssignedAccesses(IndustryAssignedAccessesEvent $event): void
    //{
    //    $this->log(
    //        $event::class,
    //        Industry::class,
    //        $this->serializer->serialize($event->role, 'json', [
    //            'circular_reference_handler' => function ($object) {
    //                return $object->getUUID();
    //            },
    //        ])
    //    );
    //}
    //
    //public function onAssignedPermissions(IndustryAssignedPermissionsEvent $event): void
    //{
    //    $this->log($event::class, Industry::class, $this->serializer->serialize($event->data, 'json'));
    //}
    //
    //public function onMultipleDeleted(IndustryMultipleDeletedEvent $event): void
    //{
    //    $this->log($event::class, Industry::class, $this->serializer->serialize($event->roles, 'json'));
    //}


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
