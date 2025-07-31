<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Action\Employee;

use App\Module\Company\Application\Command\Employee\CreateEmployeeCommand;
use App\Module\Company\Application\Validator\ContractType\ContractTypeValidator;
use App\Module\Company\Application\Validator\Department\DepartmentValidator;
use App\Module\Company\Application\Validator\Employee\EmployeeValidator;
use App\Module\Company\Application\Validator\Position\PositionValidator;
use App\Module\Company\Application\Validator\Role\RoleValidator;
use App\Module\Company\Domain\DTO\Employee\CreateDTO;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class CreateEmployeeAction
{
    public function __construct(
        private MessageBusInterface $commandBus,
        private DepartmentValidator $departmentValidator,
        private EmployeeValidator $employeeValidator,
        private PositionValidator $positionValidator,
        private ContractTypeValidator $contractTypeValidator,
        private RoleValidator $roleValidator,
    )
    {
    }

    public function execute(CreateDTO $createDTO): void
    {
        $this->employeeValidator->isEmployeeAlreadyExists($createDTO->email, $createDTO->pesel);
        $this->departmentValidator->isDepartmentExists($createDTO->departmentUUID);
        $this->positionValidator->isPositionExists($createDTO->positionUUID);
        $this->contractTypeValidator->isContractTypeExists($createDTO->contractTypeUUID);
        $this->roleValidator->isRoleExists($createDTO->roleUUID);
        if (null !== $createDTO->parentEmployeeUUID) {
            $this->employeeValidator->isEmployeeExists($createDTO->parentEmployeeUUID);
        }

        $this->commandBus->dispatch(
            new CreateEmployeeCommand(
                $createDTO->departmentUUID,
                $createDTO->positionUUID,
                $createDTO->contractTypeUUID,
                $createDTO->roleUUID,
                $createDTO->parentEmployeeUUID,
                $createDTO->externalUUID,
                $createDTO->email,
                $createDTO->firstName,
                $createDTO->lastName,
                $createDTO->pesel,
                $createDTO->employmentFrom,
                $createDTO->employmentTo,
                $createDTO->active,
                $createDTO->phones,
                $createDTO->address
            )
        );
    }
}
