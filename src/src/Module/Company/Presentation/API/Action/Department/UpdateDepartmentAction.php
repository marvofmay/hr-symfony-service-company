<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Action\Department;

use App\Module\Company\Application\Command\Department\UpdateDepartmentCommand;
use App\Module\Company\Application\Validator\Company\CompanyValidator;
use App\Module\Company\Application\Validator\Department\DepartmentValidator;
use App\Module\Company\Domain\DTO\Department\UpdateDTO;
use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class UpdateDepartmentAction
{
    public function __construct(
        private MessageBusInterface $commandBus,
        private DepartmentReaderInterface $departmentReaderRepository,
        private CompanyValidator $companyValidator,
        private DepartmentValidator $departmentValidator,
    ) {
    }

    public function execute(UpdateDTO $updateDTO, string $uuid): void
    {
        $department = $this->departmentReaderRepository->getDepartmentByUUID($uuid);

        $this->companyValidator->isCompanyExists($updateDTO->companyUUID);
        $this->departmentValidator->isDepartmentExistsWithName($updateDTO->name, $uuid);
        if (null !== $updateDTO->parentDepartmentUUID) {
            $this->departmentValidator->isDepartmentExists($updateDTO->parentDepartmentUUID);
        }

        $this->commandBus->dispatch(
            new UpdateDepartmentCommand(
                $department,
                $updateDTO->name,
                $updateDTO->description,
                $updateDTO->active,
                $updateDTO->companyUUID,
                $updateDTO->parentDepartmentUUID,
                $updateDTO->phones,
                $updateDTO->emails,
                $updateDTO->websites,
                $updateDTO->address
            )
        );
    }
}
