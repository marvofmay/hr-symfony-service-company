<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Department;

use App\Module\Company\Application\Command\Department\UpdateDepartmentCommand;
use App\Module\Company\Domain\Entity\Company;
use App\Module\Company\Domain\Entity\Department;
use App\Module\Company\Domain\Interface\Company\CompanyReaderInterface;
use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;
use App\Module\Company\Domain\Interface\Department\DepartmentWriterInterface;

class DepartmentUpdater
{
    public function __construct(
        private Department $department,
        private readonly CompanyReaderInterface $companyReaderRepository,
        private readonly DepartmentReaderInterface $departmentReaderRepository,
        private readonly DepartmentWriterInterface $departmentWriterRepository,
   ) {}

    public function update(UpdateDepartmentCommand $command): void
    {
        $this->department = $command->department;
        $this->department->setName($command->name);
        $this->department->setActive($command->active);
        $this->setCompany($command->companyUUID);
        $this->setParentDepartment($command->parentDepartmentUUID);

        $this->departmentWriterRepository->updateDepartmentInDB($this->department);
    }

    private function getCompany(?string $companyUUID): Company
    {
        return $this->companyReaderRepository->getCompanyByUUID($companyUUID);
    }

    private function getParentDepartment(?string $parentDepartmentUUID): ?Department
    {
        if (null === $parentDepartmentUUID) {
            return null;
        }

        return $this->departmentReaderRepository->getDepartmentByUUID($parentDepartmentUUID);
    }

    private function setCompany(string $companyUUID): void
    {
        if ($this->department->getCompany()->getUUID()->toString() !== $companyUUID) {
            $this->department->removeParentDepartment();
            $this->department->setCompany($this->getCompany($companyUUID));
        }
    }

    private function setParentDepartment(?string $parentDepartmentUUID): void
    {
        if (null === $parentDepartmentUUID) {
            return;
        }

        if (null == $this->department->getParentDepartment()) {
            $this->department->setParentDepartment($this->getParentDepartment($parentDepartmentUUID));
        } else {
            if ($this->department->getParentDepartment()->getUUID()->toString() !== $parentDepartmentUUID) {
                $this->department->removeParentDepartment();
                $this->department->setParentDepartment($this->getParentDepartment($parentDepartmentUUID));
            }
        }
    }
}