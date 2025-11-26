<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Transformer\ContractType;

use App\Common\Domain\Enum\TimeStampableEntityFieldEnum;
use App\Common\Domain\Interface\DataTransformerInterface;
use App\Module\Company\Application\QueryHandler\ContractType\ListContractTypesQueryHandler;
use App\Module\Company\Domain\Entity\ContractType;
use App\Module\Company\Domain\Entity\Employee;
use App\Module\Company\Domain\Enum\ContractType\ContractTypeEntityFieldEnum;
use App\Module\Company\Domain\Enum\ContractType\ContractTypeEntityRelationFieldEnum;
use Doctrine\Common\Collections\Collection;

class ContractTypeDataTransformer implements DataTransformerInterface
{
    public static function supports(): string
    {
        return ListContractTypesQueryHandler::class;
    }

    public function transformToArray(ContractType $contractType, array $includes = []): array
    {
        $data = [
            ContractTypeEntityFieldEnum::UUID->value => $contractType->getUUID()->toString(),
            ContractTypeEntityFieldEnum::NAME->value => $contractType->getName(),
            ContractTypeEntityFieldEnum::DESCRIPTION->value => $contractType->getDescription(),
            ContractTypeEntityFieldEnum::ACTIVE->value => $contractType->isActive(),
            TimeStampableEntityFieldEnum::CREATED_AT->value => $contractType->createdAt?->format('Y-m-d H:i:s'),
            TimeStampableEntityFieldEnum::UPDATED_AT->value => $contractType->getUpdatedAt()?->format('Y-m-d H:i:s'),
            TimeStampableEntityFieldEnum::DELETED_AT->value => $contractType->getDeletedAt()?->format('Y-m-d H:i:s'),
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
            ContractTypeEntityRelationFieldEnum::EMPLOYEES->value => $this->transformEmployees($contractType->getEmployees()),
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
