<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Transformer\Employee;

use App\Module\Company\Domain\Entity\Company;
use App\Module\Company\Domain\Entity\ContractType;
use App\Module\Company\Domain\Entity\Department;
use App\Module\Company\Domain\Entity\Employee;
use App\Module\Company\Domain\Entity\Position;
use App\Module\Company\Domain\Entity\Role;

class EmployeeDataTransformer
{
    public function transformToArray(Employee $employee, array $includes = []): array
    {
        $data = [
            Employee::COLUMN_UUID => $employee->getUUID()->toString(),
            Employee::COLUMN_FIRST_NAME => $employee->getFirstName(),
            Employee::COLUMN_LAST_NAME => $employee->getLastName(),
            Employee::COLUMN_PESEL => $employee->getPesel(),
            Employee::COLUMN_EMPLOYMENT_FROM => $employee->getEmploymentFrom(),
            Employee::COLUMN_EMPLOYMENT_TO => $employee->getEmploymentTo(),
            Employee::COLUMN_ACTIVE => $employee->getActive(),
            Employee::COLUMN_CREATED_AT => $employee->getCreatedAt()?->format('Y-m-d H:i:s'),
            Employee::COLUMN_UPDATED_AT => $employee->getUpdatedAt()?->format('Y-m-d H:i:s'),
            Employee::COLUMN_DELETED_AT => $employee->getDeletedAt()?->format('Y-m-d H:i:s'),
        ];

        foreach ($includes as $relation) {
            if (in_array($relation, Employee::getRelations(), true)) {
                $data[$relation] = $this->transformRelation($employee, $relation);
            }
        }

        return $data;
    }

    private function transformRelation(Employee $employee, string $relation): ?array
    {
        return match ($relation) {
            Employee::RELATION_COMPANY => $this->transformCompany($employee->getDepartment()->getCompany()),
            Employee::RELATION_DEPARTMENT => $this->transformDepartment($employee->getDepartment()),
            Employee::RELATION_PARENT_EMPLOYEE => $this->transformParentEmployee($employee->getParentEmployee()),
            Employee::RELATION_POSITION => $this->transformPosition($employee->getPosition()),
            Employee::RELATION_ROLE => $this->transformRole($employee->getRole()),
            Employee::RELATION_CONTRACT_TYPE => $this->transformContractType($employee->getContractType()),
            default => null,
        };
    }

    private function transformCompany(Company $company): ?array
    {
        return [
            Company::COLUMN_UUID => $company->getUUID()->toString(),
            Company::COLUMN_FULL_NAME => $company->getFullName(),
            Company::COLUMN_SHORT_NAME => $company->getShortName(),
            Company::COLUMN_NIP => $company->getNIP(),
        ];
    }

    private function transformDepartment(Department $department): ?array
    {
        return [
            Department::COLUMN_UUID => $department->getUUID()->toString(),
            Department::COLUMN_NAME => $department->getName(),
        ];
    }

    private function transformParentEmployee(?Employee $parentEmployee): ?array
    {
        if (!$parentEmployee) {
            return null;
        }

        return [
            Employee::COLUMN_UUID => $parentEmployee->getUUID()->toString(),
            Employee::COLUMN_FIRST_NAME => $parentEmployee->getFirstName(),
            Employee::COLUMN_LAST_NAME => $parentEmployee->getLastName(),
            Employee::COLUMN_PESEL => $parentEmployee->getPesel(),
        ];
    }

    private function transformPosition(Position $position): ?array
    {
        return [
            Position::COLUMN_UUID => $position->getUUID()->toString(),
            Position::COLUMN_NAME => $position->getName(),
            Position::COLUMN_DESCRIPTION => $position->getDescription(),
        ];
    }

    private function transformRole(Role $role): ?array
    {
        return [
            Role::COLUMN_UUID => $role->getUUID()->toString(),
            Role::COLUMN_NAME => $role->getName(),
            Role::COLUMN_DESCRIPTION => $role->getDescription(),
        ];
    }

    private function transformContractType(ContractType $contractType): ?array
    {
        return [
            ContractType::COLUMN_UUID => $contractType->getUUID()->toString(),
            ContractType::COLUMN_NAME => $contractType->getName(),
            ContractType::COLUMN_DESCRIPTION => $contractType->getDescription(),
        ];
    }
}