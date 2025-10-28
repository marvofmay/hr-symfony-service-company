<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Transformer\Role;

use App\Module\Company\Domain\Entity\Employee;
use App\Module\Company\Domain\Entity\Role;
use Doctrine\Common\Collections\Collection;

class RoleDataTransformer
{
    public function transformToArray(Role $role, array $includes = []): array
    {
        $data = [
            Role::COLUMN_UUID => $role->getUUID()->toString(),
            Role::COLUMN_NAME => $role->getName(),
            Role::COLUMN_DESCRIPTION => $role->getDescription(),
            Role::COLUMN_CREATED_AT => $role->getCreatedAt()->format('Y-m-d H:i:s'),
            Role::COLUMN_UPDATED_AT => $role->getUpdatedAt()?->format('Y-m-d H:i:s'),
            Role::COLUMN_DELETED_AT => $role->getDeletedAt()?->format('Y-m-d H:i:s'),
        ];

        foreach ($includes as $relation) {
            if (in_array($relation, Role::getRelations(), true)) {
                $data[$relation] = $this->transformRelation($role, $relation);
            }
        }

        return $data;
    }

    private function transformRelation(Role $role, string $relation): ?array
    {
        return match ($relation) {
            Role::RELATION_EMPLOYEES => $this->transformEmployees($role->getEmployees()),
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
}
