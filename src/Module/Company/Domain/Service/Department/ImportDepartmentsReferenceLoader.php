<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Department;

use App\Common\Infrastructure\Cache\EntityReferenceCache;
use App\Module\Company\Domain\Entity\Department;
use App\Module\Company\Domain\Enum\ContactTypeEnum;
use App\Module\Company\Domain\Enum\DepartmentImportColumnEnum;
use App\Module\Company\Domain\Interface\Company\CompanyReaderInterface;
use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;

final class ImportDepartmentsReferenceLoader
{
    public array $companies = [] {
        get {
            return $this->companies;
        }
    }
    public array $departments = [] {
        get {
            return $this->departments;
        }
    }
    public array $emailsInternalCodes = [] {
        get {
            return $this->emailsInternalCodes;
        }
    }

    public function __construct(
        private readonly CompanyReaderInterface $companyReaderRepository,
        private readonly DepartmentReaderInterface $departmentReaderRepository,
        private readonly EntityReferenceCache $entityReferenceCache,
    ) {
    }

    public function preload(array $rows): void
    {
        $companyUUIDs = [];
        $departmentInternalCodes = [];
        $departmentEmails = [];

        foreach ($rows as $row) {
            if (!empty($row[DepartmentImportColumnEnum::COMPANY_UUID->value])) {
                $companyUUIDs[] = (string) $row[DepartmentImportColumnEnum::COMPANY_UUID->value];
            }
            if (!empty($row[DepartmentImportColumnEnum::DEPARTMENT_INTERNAL_CODE->value])) {
                $departmentInternalCodes[] = (string) $row[DepartmentImportColumnEnum::DEPARTMENT_INTERNAL_CODE->value];
            }
            if (!empty($row[DepartmentImportColumnEnum::PARENT_DEPARTMENT_INTERNAL_CODE->value])) {
                $departmentInternalCodes[] = (string) $row[DepartmentImportColumnEnum::PARENT_DEPARTMENT_INTERNAL_CODE->value];
            }
            if (!empty($row[DepartmentImportColumnEnum::EMAIL->value])) {
                $departmentEmails[] = (string) $row[DepartmentImportColumnEnum::EMAIL->value];
            }
        }

        $companyUUIDs = array_unique($companyUUIDs);
        $departmentInternalCodes = array_unique($departmentInternalCodes);
        $departmentEmails = array_unique($departmentEmails);

        $this->companies = $this->mapByUUID($this->companyReaderRepository->getCompaniesByUUID($companyUUIDs));
        $this->departments = $this->mapByInternalCode($this->departmentReaderRepository->getDepartmentsByInternalCode($departmentInternalCodes));
        $this->emailsInternalCodes = $this->mapByEmail($this->departmentReaderRepository->getDepartmentsInternalCodeByEmails($departmentEmails));
    }

    private function mapByUUID(iterable $companies): array
    {
        $map = [];
        foreach ($companies as $company) {
            $map[$company->getUUID()->toString()] = $company;
            $this->entityReferenceCache->set($company);
        }

        return $map;
    }

    private function mapByInternalCode(iterable $departments): array
    {
        $map = [];
        foreach ($departments as $department) {
            $map[$department->getInternalCode()] = $department;
            $this->entityReferenceCache->set($department);
        }

        return $map;
    }

    private function mapByEmail(iterable $items): array
    {
        $map = [];
        foreach ($items as $item) {
            $map[$item[ContactTypeEnum::EMAIL->value]] = $item[Department::COLUMN_INTERNAL_CODE];
        }

        return $map;
    }
}
