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
    public function __construct(
        private DepartmentService $departmentService,
        private CompanyReaderInterface $companyReaderRepository,
        private DepartmentReaderInterface $departmentReaderRepository
    )
    {
    }

    public function __invoke(CreateDepartmentCommand $command): void
    {
        $department = new Department();
        $department->setName($command->name);
        $department->setActive($command->active);

        $company = $this->companyReaderRepository->getCompanyByUUID($command->companyUUID);
        if ($company instanceof Company) {
            $department->setCompany($company);
        }

        if (null !== $command->parentDepartmentUUID) {
            $parentDepartment = $this->departmentReaderRepository->getDepartmentByUUID($command->parentDepartmentUUID);
            if ($parentDepartment instanceof Department) {
                $department->setParentDepartment($parentDepartment);
            }
        }

        $this->departmentService->saveDepartmentInDB($department);
    }
}
