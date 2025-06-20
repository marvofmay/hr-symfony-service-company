<?php

declare(strict_types=1);

namespace App\Module\Company\Application\QueryHandler\Role;

use App\Common\Application\QueryHandler\ListQueryHandlerAbstract;
use App\Module\Company\Application\Query\Role\ListRolesQuery;
use App\Module\Company\Domain\Entity\Role;

final class ListRolesQueryHandler extends ListQueryHandlerAbstract
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
        return Role::ALIAS;
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

    protected function getPhraseSearchColumns(): array
    {
        return [
            Role::COLUMN_NAME,
            Role::COLUMN_DESCRIPTION,
        ];
    }

    protected function getRelations(): array
    {
        return Role::getRelations();
    }
}
