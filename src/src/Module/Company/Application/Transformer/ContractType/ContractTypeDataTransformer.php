<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Transformer\ContractType;

use App\Module\Company\Domain\Entity\ContractType;
use App\Module\Company\Domain\Entity\Employee;
use Doctrine\Common\Collections\Collection;

class ContractTypeDataTransformer
{
    public function transformToArray(ContractType $contractType, array $includes = []): array
    {
        $data = [
            ContractType::COLUMN_UUID => $contractType->getUUID()->toString(),
            ContractType::COLUMN_NAME => $contractType->getName(),
            ContractType::COLUMN_ACTIVE => $contractType->getActive(),
            ContractType::COLUMN_DESCRIPTION => $contractType->getDescription(),
            ContractType::COLUMN_CREATED_AT => $contractType->getCreatedAt()?->format('Y-m-d H:i:s'),
            ContractType::COLUMN_UPDATED_AT => $contractType->getUpdatedAt()?->format('Y-m-d H:i:s'),
            ContractType::COLUMN_DELETED_AT => $contractType->getDeletedAt()?->format('Y-m-d H:i:s'),
        ];

        foreach ($includes as $relation) {
            if (in_array($relation, ContractType::getRelations(), true)) {
                $data[$relation] = $this->transformRelation($contractType, $relation);
            }
        }

        return $data;
    }

    private function transformRelation(ContractType $contractType, string $relation): ?array
    {
        return match ($relation) {
            ContractType::RELATION_EMPLOYEES => $this->transformEmployees($contractType->getEmployees()),
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
