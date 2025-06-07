<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Action\Employee;

use App\Module\Company\Application\Command\Employee\UpdateEmployeeCommand;
use App\Module\Company\Domain\DTO\Employee\UpdateDTO;
use App\Module\Company\Domain\Interface\Employee\EmployeeReaderInterface;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class UpdateEmployeeAction
{
    public function __construct(private MessageBusInterface $commandBus, private EmployeeReaderInterface $employeeReaderRepository)
    {
    }

    public function execute(UpdateDTO $updateDTO): void
    {
        $employee = $this->employeeReaderRepository->getEmployeeByUUID($updateDTO->getUUID());
        $this->commandBus->dispatch(
            new UpdateEmployeeCommand(
                $employee,
                $updateDTO->getDepartmentUUID(),
                $updateDTO->getPositionUUID(),
                $updateDTO->getContractTypeUUID(),
                $updateDTO->getRoleUUID(),
                $updateDTO->getParentEmployeeUUID(),
                $updateDTO->getExternalUUID(),
                $updateDTO->getEmail(),
                $updateDTO->getFirstName(),
                $updateDTO->getLastName(),
                $updateDTO->getPESEL(),
                $updateDTO->getEmploymentFrom(),
                $updateDTO->getEmploymentTo(),
                $updateDTO->getActive(),
                $updateDTO->getPhones(),
                $updateDTO->getAddress()
            )
        );
    }
}
