<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Department;

use App\Common\Domain\Abstract\CommandHandlerAbstract;
use App\Common\Domain\Entity\EventStore;
use App\Common\Domain\Service\EventStore\EventStoreCreator;
use App\Module\Company\Application\Command\Department\DeleteMultipleDepartmentsCommand;
use App\Module\Company\Domain\Aggregate\Department\DepartmentAggregate;
use App\Module\Company\Domain\Aggregate\Department\ValueObject\DepartmentUUID;
use App\Module\Company\Domain\Event\Department\DepartmentMultipleDeletedEvent;
use App\Module\Company\Domain\Interface\Department\DepartmentAggregateReaderInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsMessageHandler(bus: 'command.bus')]
final class DeleteMultipleDepartmentsCommandHandler extends CommandHandlerAbstract
{
    public function __construct(
        private readonly EventDispatcherInterface                                               $eventDispatcher,
        private readonly DepartmentAggregateReaderInterface                                     $departmentAggregateReaderRepository,
        private readonly EventStoreCreator                                                      $eventStoreCreator,
        private readonly Security                                                               $security,
        private readonly SerializerInterface                                                    $serializer,
        #[AutowireIterator(tag: 'app.department.delete_multiple.validator')] protected iterable $validators,
    ) {
    }

    public function __invoke(DeleteMultipleDepartmentsCommand $command): void
    {
        $this->validate($command);

        $deletedUUIDs = [];
        foreach ($command->selectedUUIDs as $departmentUUID) {
            $uuid = DepartmentUUID::fromString($departmentUUID);
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
