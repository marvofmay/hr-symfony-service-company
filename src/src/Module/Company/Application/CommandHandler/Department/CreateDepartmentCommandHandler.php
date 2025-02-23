<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Department;

use App\Module\Company\Application\Command\Department\CreateDepartmentCommand;
use App\Module\Company\Domain\Entity\Company;
use App\Module\Company\Domain\Entity\Department;
use App\Module\Company\Domain\Interface\Company\CompanyReaderInterface;
use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;
use App\Module\Company\Domain\Service\Department\DepartmentService;

readonly class CreateDepartmentCommandHandler
{
    private CreateDepartmentCommand $command;

    public function __construct(
        private DepartmentService $departmentService,
        private CompanyReaderInterface $companyReaderRepository,
        private DepartmentReaderInterface $departmentReaderRepository
    )
    {

    }

    public function __invoke(CreateDepartmentCommand $command): void
    {
        $this->command = $command;

        $department = new Department();
        $department->setName($this->command->name);
        $department->setActive($this->command->active);
        $department->setCompany($this->getCompany());

        if (null !== $command->parentDepartmentUUID) {
            $department->setParentDepartment($this->getDepartment());
        }

        $this->departmentService->saveDepartmentInDB($department);
    }

    private function getCompany(): Company
    {
        return $this->companyReaderRepository->getCompanyByUUID($this->command->companyUUID);
    }

    private function getDepartment(): Department
    {
       return $this->departmentReaderRepository->getDepartmentByUUID($this->command->parentDepartmentUUID);
    }
}
