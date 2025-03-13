<?php

declare(strict_types=1);

namespace App\Module\Company\Application\QueryHandler\ContractType;

use App\Common\Application\QueryHandler\ListQueryHandlerAbstract;
use App\Module\Company\Application\Query\ContractType\ListContractTypesQuery;
use App\Module\Company\Domain\Entity\ContractType;

class ListContractTypesQueryHandler extends ListQueryHandlerAbstract
{
    public function __invoke(ListContractTypesQuery $query): array
    {
        return $this->handle($query);
    }

    protected function getEntityClass(): string
    {
        return ContractType::class;
    }

    protected function getAlias(): string
    {
        return 'contractType';
    }

    protected function getDefaultOrderBy(): string
    {
        return ContractType::COLUMN_CREATED_AT;
    }

    protected function getAllowedFilters(): array
    {
        return [
            ContractType::COLUMN_NAME,
            ContractType::COLUMN_DESCRIPTION,
            ContractType::COLUMN_ACTIVE,
            ContractType::COLUMN_CREATED_AT,
            ContractType::COLUMN_UPDATED_AT,
            ContractType::COLUMN_DELETED_AT,
        ];
    }

    protected function getPhraseSearchColumns(): array
    {
        return [
            ContractType::COLUMN_NAME,
            ContractType::COLUMN_DESCRIPTION,
        ];
    }

    protected function transformIncludes(array $items, array $includes): array
    {
        $data = array_map(fn($role) => $role->toArray(), $items);
        foreach (ContractType::getRelations() as $contractType) {
            foreach ($data as $key => $role) {
                if (!in_array($contractType, $includes) || empty($includes)) {
                    unset($data[$key][$contractType]);
                }
            }
        }

        return $data;
    }
}
