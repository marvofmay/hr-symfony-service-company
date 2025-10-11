<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Department;

use App\Module\Company\Domain\Interface\Company\CompanyReaderInterface;
use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;

final class ImportDepartmentsReferenceLoader
{
    private array $companies = [];
    private array $departments = [];

    public function __construct(
        private readonly CompanyReaderInterface $companyReaderRepository,
        private readonly DepartmentReaderInterface $departmentReaderRepository,
    ) {
    }

    public function preload(array $rows): void
    {
        $companyUUIDs = [];
        $departmentInternalCodes = [];

        foreach ($rows as $row) {
            if (!empty($row[ImportDepartmentsFromXLSX::COLUMN_COMPANY_UUID])) {
                $companyUUIDs[] = (string) $row[ImportDepartmentsFromXLSX::COLUMN_COMPANY_UUID];
            }
            if (!empty($row[ImportDepartmentsFromXLSX::COLUMN_PARENT_DEPARTMENT_INTERNAL_CODE])) {
                $departmentInternalCodes[] = (string) $row[ImportDepartmentsFromXLSX::COLUMN_PARENT_DEPARTMENT_INTERNAL_CODE];
            }
        }

        $companyUUIDs = array_unique($companyUUIDs);
        $departmentInternalCodes = array_unique($departmentInternalCodes);

        $this->companies = $this->mapByUUID($this->companyReaderRepository->getCompaniesByUUID($companyUUIDs));
        $this->departments = $this->mapByInternalCode($this->departmentReaderRepository->getDepartmentsByInternalCode($departmentInternalCodes));
    }

    public function getCompanies(): array
    {
        return $this->companies;
    }

    public function getDepartments(): array
    {
        return $this->departments;
    }

    private function mapByUUID(iterable $companies): array
    {
        $map = [];
        foreach ($companies as $company) {
            $map[$company->getUUID()->toString()] = $company;
        }

        return $map;
    }

    private function mapByInternalCode(iterable $departments): array
    {
        $map = [];
        foreach ($departments as $department) {
            $map[$department->getInternalCode()] = $department;
        }

        return $map;
    }
}
