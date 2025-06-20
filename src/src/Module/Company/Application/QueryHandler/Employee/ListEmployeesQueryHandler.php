<?php

declare(strict_types=1);

namespace App\Module\Company\Application\QueryHandler\Employee;

use App\Common\Application\QueryHandler\ListQueryHandlerAbstract;
use App\Module\Company\Application\Query\Employee\ListEmployeesQuery;
use App\Module\Company\Domain\Entity\Employee;

class ListEmployeesQueryHandler extends ListQueryHandlerAbstract
{
    public function __invoke(ListEmployeesQuery $query): array
    {
        return $this->handle($query);
    }

    protected function getEntityClass(): string
    {
        return Employee::class;
    }

    protected function getAlias(): string
    {
        return 'employee';
    }

    protected function getDefaultOrderBy(): string
    {
        return Employee::COLUMN_CREATED_AT;
    }

    protected function getAllowedFilters(): array
    {
        return [
            Employee::COLUMN_FIRST_NAME,
            Employee::COLUMN_LAST_NAME,
            Employee::COLUMN_ACTIVE,
            Employee::COLUMN_CREATED_AT,
            Employee::COLUMN_UPDATED_AT,
            Employee::COLUMN_DELETED_AT,
        ];
    }

    protected function getPhraseSearchColumns(): array
    {
        return [
            Employee::COLUMN_FIRST_NAME,
            Employee::COLUMN_LAST_NAME,
        ];
    }

    protected function transformIncludes(array $items, array $includes): array
    {
        $data = array_map(fn ($employee) => $employee->toArray(), $items);
        foreach (Employee::getRelations() as $relation) {
            foreach ($data as $key => $employee) {
                if (!in_array($relation, $includes) || empty($includes)) {
                    unset($data[$key][$relation]);
                }
            }
        }

        return $data;
    }

    protected function getRelations(): array
    {
        return Employee::getRelations();
    }
}
