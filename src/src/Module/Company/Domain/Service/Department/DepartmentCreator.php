<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Department;

use App\Module\Company\Application\Command\Department\CreateDepartmentCommand;
use App\Module\Company\Domain\Entity\Company;
use App\Module\Company\Domain\Entity\Department;
use App\Module\Company\Domain\Interface\Company\CompanyReaderInterface;
use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;
use App\Module\Company\Domain\Interface\Department\DepartmentWriterInterface;

class DepartmentCreator
{
    public function __construct(
        public Department $department,
        private readonly CompanyReaderInterface $companyReaderRepository,
        private readonly DepartmentReaderInterface $departmentReaderRepository,
        private readonly DepartmentWriterInterface $departmentWriterRepository,
   ) {}

    public function create(CreateDepartmentCommand $command): void
    {
        $this->department->setName($command->name);
        $this->department->setActive($command->active);
        $this->setCompany($command->companyUUID);
        $this->setParentDepartment($command->parentDepartmentUUID);

        $this->departmentWriterRepository->saveDepartmentInDB($this->department);
    }

    private function getCompany(?string $companyUUID): Company
    {
        return $this->companyReaderRepository->getCompanyByUUID($companyUUID);
    }

    private function getParentDepartment(string $parentDepartmentUUID): Department
    {
        return $this->departmentReaderRepository->getDepartmentByUUID($parentDepartmentUUID);
    }

    private function setCompany(string $companyUUID): void
    {
        $this->department->setCompany($this->getCompany($companyUUID));
    }

    private function setParentDepartment(?string $parentDepartmentUUID): void
    {
        if (null === $parentDepartmentUUID) {
            return;
        }

        $this->department->setParentDepartment($this->getParentDepartment($parentDepartmentUUID));
    }
}