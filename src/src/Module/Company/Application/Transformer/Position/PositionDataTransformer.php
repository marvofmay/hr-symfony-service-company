<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Transformer\Position;

use App\Module\Company\Domain\Entity\Department;
use App\Module\Company\Domain\Entity\Employee;
use App\Module\Company\Domain\Entity\Position;
use Doctrine\Common\Collections\Collection;

class PositionDataTransformer
{
    public function transformToArray(Position $position, array $includes = []): array
    {
        $data = [
            Position::COLUMN_UUID => $position->getUUID()->toString(),
            Position::COLUMN_NAME => $position->getName(),
            Position::COLUMN_DESCRIPTION => $position->getDescription(),
            Position::COLUMN_ACTIVE => $position->getActive(),
            Position::COLUMN_CREATED_AT => $position->getCreatedAt()?->format('Y-m-d H:i:s'),
            Position::COLUMN_UPDATED_AT => $position->getUpdatedAt()?->format('Y-m-d H:i:s'),
            Position::COLUMN_DELETED_AT => $position->getDeletedAt()?->format('Y-m-d H:i:s'),
        ];

        foreach ($includes as $relation) {
            if (in_array($relation, Position::getRelations(), true)) {
                $data[$relation] = $this->transformRelation($position, $relation);
            }
        }

        return $data;
    }

    private function transformRelation(Position $position, string $relation): ?array
    {
        return match ($relation) {
            Position::RELATION_EMPLOYEES => $this->transformEmployees($position->getEmployees()),
            Position::RELATION_DEPARTMENTS => $this->transformDepartments($position->getDepartments()),
            default => null,
        };
    }

    private function transformEmployees(?Collection $employees): ?array
    {
        if (null === $employees || $employees->isEmpty()) {
            return null;
        }

        return array_map(
            fn (Employee $employee) => [
                Employee::COLUMN_UUID => $employee->getUUID()->toString(),
                Employee::COLUMN_FIRST_NAME => $employee->getFirstName(),
                Employee::COLUMN_LAST_NAME => $employee->getLastName(),
            ],
            $employees->toArray()
        );
    }

    private function transformDepartments(?Collection $departments): ?array
    {
        if (null === $departments || $departments->isEmpty()) {
            return null;
        }

        return array_map(
            fn (Employee $department) => [
                Department::COLUMN_UUID => $department->getUUID()->toString(),
                Department::COLUMN_NAME => $department->getName(),
                Department::COLUMN_DESCRIPTION => $department->getDescription(),
                Department::COLUMN_ACTIVE => $department->getActive(),
            ],
            $departments->toArray()
        );
    }
}
