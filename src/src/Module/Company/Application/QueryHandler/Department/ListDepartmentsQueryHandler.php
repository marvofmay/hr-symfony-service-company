<?php

declare(strict_types=1);

namespace App\Module\Company\Application\QueryHandler\Department;

use App\Common\Application\QueryHandler\ListQueryHandlerAbstract;
use App\Module\Company\Application\Query\Department\ListDepartmentsQuery;
use App\Module\Company\Domain\Entity\Department;

class ListDepartmentsQueryHandler extends ListQueryHandlerAbstract
{
    public function __invoke(ListDepartmentsQuery $query): array
    {
      return $this->handle($query);
    }

    protected function getEntityClass(): string
    {
        return Department::class;
    }

    protected function getAlias(): string
    {
        return Department::ALIAS;
    }

    protected function getDefaultOrderBy(): string
    {
        return Department::COLUMN_CREATED_AT;
    }

    protected function getAllowedFilters(): array
    {
        return [
            Department::COLUMN_NAME,
            Department::COLUMN_DESCRIPTION,
            Department::COLUMN_ACTIVE,
            Department::COLUMN_CREATED_AT,
            Department::COLUMN_UPDATED_AT,
            Department::COLUMN_DELETED_AT,
        ];
    }

    protected function getPhraseSearchColumns(): array
    {
        return [
            Department::COLUMN_NAME,
            Department::COLUMN_DESCRIPTION,
        ];
    }

    protected function getRelations(): array
    {
        return Department::getRelations();
    }
}
