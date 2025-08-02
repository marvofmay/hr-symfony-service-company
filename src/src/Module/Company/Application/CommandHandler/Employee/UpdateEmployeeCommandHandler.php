<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Employee;

use App\Common\Domain\Entity\EventStore;
use App\Common\Domain\Service\EventStore\EventStoreCreator;
use App\Module\Company\Application\Command\Employee\UpdateEmployeeCommand;
use App\Module\Company\Domain\Aggregate\Department\ValueObject\DepartmentUUID;
use App\Module\Company\Domain\Aggregate\Employee\EmployeeAggregate;
use App\Module\Company\Domain\Aggregate\Employee\ValueObject\ContractTypeUUID;
use App\Module\Company\Domain\Aggregate\Employee\ValueObject\EmployeeUUID;
use App\Module\Company\Domain\Aggregate\Employee\ValueObject\EmploymentFrom;
use App\Module\Company\Domain\Aggregate\Employee\ValueObject\EmploymentTo;
use App\Module\Company\Domain\Aggregate\Employee\ValueObject\FirstName;
use App\Module\Company\Domain\Aggregate\Employee\ValueObject\LastName;
use App\Module\Company\Domain\Aggregate\Employee\ValueObject\PESEL;
use App\Module\Company\Domain\Aggregate\Employee\ValueObject\PositionUUID;
use App\Module\Company\Domain\Aggregate\Employee\ValueObject\RoleUUID;
use App\Module\Company\Domain\Aggregate\ValueObject\Address;
use App\Module\Company\Domain\Aggregate\ValueObject\Emails;
use App\Module\Company\Domain\Aggregate\ValueObject\Phones;
use App\Module\Company\Domain\Interface\Employee\EmployeeAggregateReaderInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsMessageHandler(bus: 'command.bus')]
final readonly class UpdateEmployeeCommandHandler
{
    public function __construct(
        private EventStoreCreator $eventStoreCreator,
        private Security $security,
        private SerializerInterface $serializer,
        private EventDispatcherInterface $eventDispatcher,
        private EmployeeAggregateReaderInterface $employeeAggregateReaderRepository,
    )
    {
    }

    public function __invoke(UpdateEmployeeCommand $command): void
    {
        $employeeAggregate = $this->employeeAggregateReaderRepository->getEmployeeAggregateByUUID(
            EmployeeUUID::fromString($command->employee->getUUID()->toString())
        );

        $employeeAggregate->update(
            FirstName::fromString($command->firstName),
            LastName::fromString($command->lastName),
            PESEL::fromString($command->pesel),
            EmploymentFrom::fromString($command->employmentFrom),
            DepartmentUUID::fromString($command->departmentUUID),
            PositionUUID::fromString($command->positionUUID),
            ContractTypeUUID::fromString($command->contractTypeUUID),
            RoleUUID::fromString($command->roleUUID),
            Emails::fromArray([$command->email]),
            Address::fromDTO($command->address),
            $command->externalUUID,
            $command->active,
            Phones::fromArray($command->phones),
            $command->parentEmployeeUUID ? EmployeeUUID::fromString($command->parentEmployeeUUID) : null,
            $command->employmentTo ? EmploymentTo::fromString($command->employmentTo, EmploymentFrom::fromString($command->employmentFrom)) : null,
        );

        $events = $employeeAggregate->pullEvents();
        foreach ($events as $event) {
            $this->eventStoreCreator->create(
                new EventStore(
                    $event->uuid->toString(),
                    $event::class,
                    EmployeeAggregate::class,
                    $this->serializer->serialize($event, 'json'),
                    $this->security->getUser()->getEmployee()?->getUUID(),
                )
            );

            $this->eventDispatcher->dispatch($event);
        }
    }
}
