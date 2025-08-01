<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Action\Department;

use App\Module\Company\Application\Command\Department\CreateDepartmentCommand;
use App\Module\Company\Application\Validator\Company\CompanyValidator;
use App\Module\Company\Application\Validator\Department\DepartmentValidator;
use App\Module\Company\Domain\DTO\Department\CreateDTO;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class CreateDepartmentAction
{
    public function __construct(
        private MessageBusInterface $commandBus,
        private CompanyValidator $companyValidator,
        private DepartmentValidator $departmentValidator,
    )
    {
    }

    public function execute(CreateDTO $createDTO): void
    {
        $this->companyValidator->isCompanyExists($createDTO->companyUUID);
        $this->departmentValidator->isDepartmentExistsWithName($createDTO->name);
        if (null !== $createDTO->parentDepartmentUUID) {
            $this->departmentValidator->isDepartmentExists($createDTO->parentDepartmentUUID);
        }

        $this->commandBus->dispatch(
            new CreateDepartmentCommand(
                $createDTO->name,
                $createDTO->description,
                $createDTO->active,
                $createDTO->companyUUID,
                $createDTO->parentDepartmentUUID,
                $createDTO->phones,
                $createDTO->emails,
                $createDTO->websites,
                $createDTO->address
            )
        );
    }
}
