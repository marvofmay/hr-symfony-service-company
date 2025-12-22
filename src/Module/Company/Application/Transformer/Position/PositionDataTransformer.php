<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Transformer\Position;

use App\Common\Domain\Enum\TimeStampableEntityFieldEnum;
use App\Common\Domain\Interface\DataTransformerInterface;
use App\Module\Company\Application\QueryHandler\Position\ListPositionsQueryHandler;
use App\Module\Company\Domain\Entity\Department;
use App\Module\Company\Domain\Entity\Employee;
use App\Module\Company\Domain\Entity\Position;
use App\Module\Company\Domain\Enum\Position\PositionEntityFieldEnum;
use App\Module\Company\Domain\Enum\Position\PositionEntityRelationFieldEnum;
use Doctrine\Common\Collections\Collection;

class PositionDataTransformer implements DataTransformerInterface
{
    public static function supports(): string
    {
        return ListPositionsQueryHandler::class;
    }

    public function transformToArray(Position $position, array $includes = []): array
    {
        $data = [
            PositionEntityFieldEnum::UUID->value => $position->getUUID()->toString(),
            PositionEntityFieldEnum::NAME->value => $position->getName(),
            PositionEntityFieldEnum::DESCRIPTION->value => $position->getDescription(),
            PositionEntityFieldEnum::ACTIVE->value => $position->isActive(),
            TimeStampableEntityFieldEnum::CREATED_AT->value => $position->getCreatedAt()->format('Y-m-d H:i:s'),
            TimeStampableEntityFieldEnum::UPDATED_AT->value => $position->getUpdatedAt()?->format('Y-m-d H:i:s'),
            TimeStampableEntityFieldEnum::DELETED_AT->value => $position->getDeletedAt()?->format('Y-m-d H:i:s'),
        ];

        $x = Position::getRelations();

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
            PositionEntityRelationFieldEnum::EMPLOYEES->value => $this->transformEmployees($position->getEmployees()),
            PositionEntityRelationFieldEnum::POSITION_DEPARTMENTS->value => $this->transformDepartments($position->getDepartments()),
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
            fn (Department $department) => [
                Department::COLUMN_UUID => $department->getUUID()->toString(),
                Department::COLUMN_NAME => $department->getName(),
                Department::COLUMN_INTERNAL_CODE => $department->getInternalCode(),
                Department::COLUMN_DESCRIPTION => $department->getDescription(),
                Department::COLUMN_ACTIVE => $department->getActive(),
                Department::COLUMN_CREATED_AT => $department->createdAt?->format('Y-m-d H:i:s'),
                Department::COLUMN_UPDATED_AT => $department->getUpdatedAt()?->format('Y-m-d H:i:s'),
                Department::COLUMN_DELETED_AT => $department->getDeletedAt()?->format('Y-m-d H:i:s'),
            ],
            $departments->toArray()
        );
    }
}
