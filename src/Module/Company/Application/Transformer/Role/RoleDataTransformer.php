<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Transformer\Role;

use App\Common\Domain\Enum\TimeStampableEntityFieldEnum;
use App\Common\Domain\Interface\DataTransformerInterface;
use App\Module\Company\Application\QueryHandler\Role\ListRolesQueryHandler;
use App\Module\Company\Domain\Entity\Employee;
use App\Module\Company\Domain\Entity\Role;
use App\Module\Company\Domain\Enum\Role\RoleEntityFieldEnum;
use App\Module\Company\Domain\Enum\Role\RoleEntityRelationFieldEnum;
use Doctrine\Common\Collections\Collection;

class RoleDataTransformer implements DataTransformerInterface
{
    public static function supports(): string
    {
        return ListRolesQueryHandler::class;
    }

    public function transformToArray(Role $role, array $includes = []): array
    {
        $data = [
            RoleEntityFieldEnum::UUID->value => $role->getUUID()->toString(),
            RoleEntityFieldEnum::NAME->value => $role->getName(),
            RoleEntityFieldEnum::DESCRIPTION->value => $role->getDescription(),
            TimeStampableEntityFieldEnum::CREATED_AT->value => $role->getCreatedAt()->format('Y-m-d H:i:s'),
            TimeStampableEntityFieldEnum::UPDATED_AT->value => $role->getUpdatedAt()?->format('Y-m-d H:i:s'),
            TimeStampableEntityFieldEnum::DELETED_AT->value => $role->getDeletedAt()?->format('Y-m-d H:i:s'),
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
            RoleEntityRelationFieldEnum::EMPLOYEES->value => $this->transformEmployees($role->getEmployees()),
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
