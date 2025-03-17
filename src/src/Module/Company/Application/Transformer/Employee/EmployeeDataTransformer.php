<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Transformer\Employee;

use App\Module\Company\Domain\Entity\Employee;

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
            //Employee::RELATION_COMPANY => $this->transformCompany($employee->getCompany()),
            //Employee::RELATION_DEPARTMENT => $this->transformDepartment($employee->getDepartment()),
            //Employee::RELATION_PARENT_EMPLOYEE => $this->transformParentEmployee($employee->getParentEmployee()),
            //Employee::RELATION_POSITION => $this->transformPosition($employee->getPosition()),
            //Employee::RELATION_ROLE => $this->transformRole($employee->getRole()),
            //Employee::RELATION_CONTRACT_TYPE => $this->transformContractType($employee->getContractType()),
            default => null,
        };
    }
}