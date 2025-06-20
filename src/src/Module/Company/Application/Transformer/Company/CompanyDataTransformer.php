<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Transformer\Company;

use App\Module\Company\Domain\Entity\Company;
use App\Module\Company\Domain\Entity\Department;
use App\Module\Company\Domain\Entity\Employee;
use App\Module\Company\Domain\Entity\Industry;

class CompanyDataTransformer
{
    public function transformToArray(Company $company, array $includes = []): array
    {
        $data = [
            Company::COLUMN_UUID => $company->getUUID()->toString(),
            Company::COLUMN_FULL_NAME => $company->getFullName(),
            Company::COLUMN_SHORT_NAME => $company->getShortName(),
            Company::COLUMN_NIP => $company->getNIP(),
            Company::COLUMN_REGON => $company->getREGON(),
            Company::COLUMN_DESCRIPTION => $company->getDescription(),
            Company::COLUMN_ACTIVE => $company->getActive(),
            Company::COLUMN_CREATED_AT => $company->getCreatedAt()?->format('Y-m-d H:i:s'),
            Company::COLUMN_UPDATED_AT => $company->getUpdatedAt()?->format('Y-m-d H:i:s'),
            Company::COLUMN_DELETED_AT => $company->getDeletedAt()?->format('Y-m-d H:i:s'),
        ];

        foreach ($includes as $relation) {
            if (in_array($relation, Company::getRelations(), true)) {
                $data[$relation] = $this->transformRelation($company, $relation);
            }
        }

        return $data;
    }

    private function transformRelation(Company $company, string $relation): ?array
    {
        return match ($relation) {
            Company::RELATION_DEPARTMENTS => $this->transformDepartments($company->getDepartments()),
            Company::RELATION_EMPLOYEES => $this->transformEmployees($company->getEmployees()),
            Company::RELATION_INDUSTRY => $this->transformIndustry($company->getIndustry()),
            Company::RELATION_PARENT_COMPANY => $this->transformParentCompany($company->getParentCompany()),
            default => null,
        };
    }

    private function transformDepartments($departments): ?array
    {
        if (null === $departments || $departments->isEmpty()) {
            return null;
        }

        return array_map(
            fn (Department $department) => [
                Department::COLUMN_UUID => $department->getUUID()->toString(),
                Department::COLUMN_NAME => $department->getName(),
                Department::COLUMN_DESCRIPTION => $department->getDescription(),
                Department::COLUMN_ACTIVE => $department->getActive(),
            ],
            $departments->toArray()
        );
    }

    private function transformEmployees($employees): ?array
    {
        if (null === $employees || $employees->isEmpty()) {
            return null;
        }

        return array_map(
            fn (Employee $employee) => [
                Employee::COLUMN_UUID => $employee->getUUID()->toString(),
                Employee::COLUMN_FIRST_NAME => $employee->getFirstName(),
                Employee::COLUMN_LAST_NAME => $employee->getLastName(),
                Employee::COLUMN_PESEL => $employee->getPESEL(),
            ],
            $employees->toArray()
        );
    }

    private function transformIndustry(?Industry $industry): ?array
    {
        if (!$industry) {
            return null;
        }

        return [
            Industry::COLUMN_UUID => $industry->getUUID()->toString(),
            Industry::COLUMN_NAME => $industry->getName(),
            Industry::COLUMN_DESCRIPTION => $industry->getDescription(),
        ];
    }

    private function transformParentCompany(?Company $parentCompany): ?array
    {
        if (!$parentCompany) {
            return null;
        }

        return [
            Company::COLUMN_UUID => $parentCompany->getUUID()->toString(),
            Company::COLUMN_FULL_NAME => $parentCompany->getFullName(),
            Company::COLUMN_SHORT_NAME => $parentCompany->getShortName(),
            Company::COLUMN_NIP => $parentCompany->getNIP(),
            Company::COLUMN_REGON => $parentCompany->getREGON(),
        ];
    }
}
