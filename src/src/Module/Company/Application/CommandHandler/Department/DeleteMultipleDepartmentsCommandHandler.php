<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Department;

use App\Common\Domain\Entity\EventStore;
use App\Common\Domain\Service\EventStore\EventStoreCreator;
use App\Module\Company\Application\Command\Department\DeleteMultipleDepartmentsCommand;
use App\Module\Company\Domain\Aggregate\Company\CompanyAggregate;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\CompanyUUID;
use App\Module\Company\Domain\Aggregate\Department\DepartmentAggregate;
use App\Module\Company\Domain\Aggregate\Department\ValueObject\DepartmentUUID;
use App\Module\Company\Domain\Event\Company\CompanyMultipleDeletedEvent;
use App\Module\Company\Domain\Event\Department\DepartmentMultipleDeletedEvent;
use App\Module\Company\Domain\Interface\Department\DepartmentAggregateReaderInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsMessageHandler(bus: 'command.bus')]
final readonly class DeleteMultipleDepartmentsCommandHandler
{
    public function __construct(
        private EventDispatcherInterface           $eventDispatcher,
        private DepartmentAggregateReaderInterface $departmentAggregateReaderRepository,
        private EventStoreCreator                  $eventStoreCreator,
        private Security                           $security,
        private SerializerInterface                $serializer,
    )
    {
    }

    public function __invoke(DeleteMultipleDepartmentsCommand $command): void
    {
        $deletedUUIDs = [];
        foreach ($command->departments as $department) {
            $uuid = DepartmentUUID::fromString($department->getUUID()->toString());
            $departmentAggregate = $this->departmentAggregateReaderRepository->getDepartmentAggregateByUUID($uuid);

            $departmentAggregate->delete();

            $events = $departmentAggregate->pullEvents();
            foreach ($events as $event) {
                $this->eventStoreCreator->create(
                    new EventStore(
                        $event->uuid->toString(),
                        $event::class,
                        DepartmentAggregate::class,
                        $this->serializer->serialize($event, 'json'),
                        $this->security->getUser()->getEmployee()?->getUUID(),
                    )
                );

                $this->eventDispatcher->dispatch($event);
            }

            $deletedUUIDs[] = $uuid->toString();
        }

        $multiEvent = new DepartmentMultipleDeletedEvent($deletedUUIDs);
        $this->eventStoreCreator->create(
            new EventStore(
                Uuid::uuid4()->toString(),
                $multiEvent::class,
                DepartmentAggregate::class,
                $this->serializer->serialize($multiEvent, 'json'),
                $this->security->getUser()->getEmployee()?->getUUID(),
            )
        );
    }
}
