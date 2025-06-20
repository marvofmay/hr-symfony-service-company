<?php

declare(strict_types=1);

namespace App\Module\Company\Application\QueryHandler\Position;

use App\Common\Application\QueryHandler\ListQueryHandlerAbstract;
use App\Module\Company\Application\Query\Position\ListPositionsQuery;
use App\Module\Company\Domain\Entity\Position;

class ListPositionsQueryHandler extends ListQueryHandlerAbstract
{
    public function __invoke(ListPositionsQuery $query): array
    {
        return $this->handle($query);
    }

    protected function getEntityClass(): string
    {
        return Position::class;
    }

    protected function getAlias(): string
    {
        return 'position';
    }

    protected function getDefaultOrderBy(): string
    {
        return Position::COLUMN_CREATED_AT;
    }

    protected function getAllowedFilters(): array
    {
        return [
            Position::COLUMN_NAME,
            Position::COLUMN_DESCRIPTION,
            Position::COLUMN_CREATED_AT,
            Position::COLUMN_UPDATED_AT,
            Position::COLUMN_DELETED_AT,
        ];
    }

    protected function getPhraseSearchColumns(): array
    {
        return [
            Position::COLUMN_NAME,
            Position::COLUMN_DESCRIPTION,
        ];
    }

    protected function transformIncludes(array $items, array $includes): array
    {
        $data = array_map(fn ($role) => $role->toArray(), $items);
        foreach (Position::getRelations() as $relation) {
            foreach ($data as $key => $role) {
                if (!in_array($relation, $includes) || empty($includes)) {
                    unset($data[$key][$relation]);
                }
            }
        }

        return $data;
    }

    protected function getRelations(): array
    {
        return Position::getRelations();
    }
}
