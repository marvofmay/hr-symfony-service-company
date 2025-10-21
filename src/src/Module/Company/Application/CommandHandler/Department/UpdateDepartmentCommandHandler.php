<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Department;

use App\Common\Domain\Abstract\CommandHandlerAbstract;
use App\Common\Domain\Entity\EventStore;
use App\Common\Domain\Service\EventStore\EventStoreCreator;
use App\Module\Company\Application\Command\Department\UpdateDepartmentCommand;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\CompanyUUID;
use App\Module\Company\Domain\Aggregate\Department\DepartmentAggregate;
use App\Module\Company\Domain\Aggregate\Department\ValueObject\DepartmentUUID;
use App\Module\Company\Domain\Aggregate\Department\ValueObject\Name;
use App\Module\Company\Domain\Aggregate\ValueObject\Address;
use App\Module\Company\Domain\Aggregate\ValueObject\Emails;
use App\Module\Company\Domain\Aggregate\ValueObject\Phones;
use App\Module\Company\Domain\Aggregate\ValueObject\Websites;
use App\Module\Company\Domain\Interface\Department\DepartmentAggregateReaderInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsMessageHandler(bus: 'command.bus')]
final class UpdateDepartmentCommandHandler extends CommandHandlerAbstract
{
    public function __construct(
        private readonly DepartmentAggregateReaderInterface                            $departmentAggregateReaderRepository,
        private readonly EventStoreCreator                                             $eventStoreCreator,
        private readonly Security                                                      $security,
        private readonly SerializerInterface                                           $serializer,
        private readonly EventDispatcherInterface                                      $eventDispatcher,
        #[AutowireIterator(tag: 'app.department.update.validator')] protected iterable $validators,
    ) {
    }

    public function __invoke(UpdateDepartmentCommand $command): void
    {
        $this->validate($command);

        $departmentAggregate = $this->departmentAggregateReaderRepository->getDepartmentAggregateByUUID(
            DepartmentUUID::fromString($command->departmentUUID)
        );

        $departmentAggregate->update(
            CompanyUUID::fromString($command->companyUUID),
            Name::fromString($command->name),
            $command->internalCode,
            Address::fromDTO($command->address),
            $command->active,
            $command->description,
            $command->phones ? Phones::fromArray($command->phones) : null,
            $command->emails ? Emails::fromArray($command->emails) : null,
            $command->websites ? Websites::fromArray($command->websites) : null,
            $command->parentDepartmentUUID ? DepartmentUUID::fromString($command->parentDepartmentUUID) : null,
        );

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
    }
}
