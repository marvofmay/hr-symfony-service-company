<?php

declare(strict_types=1);

namespace App\Module\Company\Application\QueryHandler\Role;

use App\Common\Application\QueryHandler\ListQueryHandlerAbstract;
use App\Module\Company\Application\Query\Role\ListRolesQuery;
use App\Module\Company\Domain\Entity\Role;

class ListRolesQueryHandler extends ListQueryHandlerAbstract
{
    public function __invoke(ListRolesQuery $query): array
    {
      return $this->handle($query);
    }

    protected function getEntityClass(): string
    {
        return Role::class;
    }

    protected function getAlias(): string
    {
        return 'role';
    }

    protected function getDefaultOrderBy(): string
    {
        return Role::COLUMN_CREATED_AT;
    }

    protected function getAllowedFilters(): array
    {
        return [
            Role::COLUMN_NAME,
            Role::COLUMN_DESCRIPTION,
            Role::COLUMN_CREATED_AT,
            Role::COLUMN_UPDATED_AT,
            Role::COLUMN_DELETED_AT,
        ];
    }

    protected function transformIncludes(array $items, array $includes): array
    {
        $data = array_map(fn($role) => $role->toArray(), $items);
        foreach (Role::getRelations() as $relation) {
            foreach ($data as $key => $role) {
                if (!in_array($relation, $includes) || empty($includes)) {
                    unset($data[$key][$relation]);
                }
            }
        }

        return $data;
    }
}
