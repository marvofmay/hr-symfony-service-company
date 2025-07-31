<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Action\Employee;

use App\Module\Company\Application\Command\Employee\UpdateEmployeeCommand;
use App\Module\Company\Application\Validator\ContractType\ContractTypeValidator;
use App\Module\Company\Application\Validator\Department\DepartmentValidator;
use App\Module\Company\Application\Validator\Employee\EmployeeValidator;
use App\Module\Company\Application\Validator\Position\PositionValidator;
use App\Module\Company\Application\Validator\Role\RoleValidator;
use App\Module\Company\Domain\DTO\Employee\UpdateDTO;
use App\Module\Company\Domain\Interface\Employee\EmployeeReaderInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class UpdateEmployeeAction
{
    public function __construct(
        private MessageBusInterface $commandBus,
        private EmployeeReaderInterface $employeeReaderRepository, 
        private DepartmentValidator $departmentValidator,
        private EmployeeValidator $employeeValidator,
        private PositionValidator $positionValidator,
        private ContractTypeValidator $contractTypeValidator,
        private RoleValidator $roleValidator,
    )
    {
    }

    public function execute(string $uuid, UpdateDTO $updateDTO): void
    {
        $employee = $this->employeeReaderRepository->getEmployeeByUUID($uuid);
        $this->employeeValidator->isEmployeeAlreadyExists($updateDTO->email, $updateDTO->pesel, $uuid);
        $this->departmentValidator->isDepartmentExists($updateDTO->departmentUUID);
        $this->positionValidator->isPositionExists($updateDTO->positionUUID);
        $this->contractTypeValidator->isContractTypeExists($updateDTO->contractTypeUUID);
        $this->roleValidator->isRoleExists($updateDTO->roleUUID);
        if (null !== $updateDTO->parentEmployeeUUID) {
            $this->employeeValidator->isEmployeeExists($updateDTO->parentEmployeeUUID);
        }

        $this->commandBus->dispatch(
            new UpdateEmployeeCommand(
                $employee,
                $updateDTO->departmentUUID,
                $updateDTO->positionUUID,
                $updateDTO->contractTypeUUID,
                $updateDTO->roleUUID,
                $updateDTO->parentEmployeeUUID,
                $updateDTO->externalUUID,
                $updateDTO->email,
                $updateDTO->firstName,
                $updateDTO->lastName,
                $updateDTO->pesel,
                $updateDTO->employmentFrom,
                $updateDTO->employmentTo,
                $updateDTO->active,
                $updateDTO->phones,
                $updateDTO->address
            )
        );
    }
}
