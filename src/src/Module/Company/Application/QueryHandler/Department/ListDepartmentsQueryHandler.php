<?php

declare(strict_types=1);

namespace App\Module\Company\Application\QueryHandler\Department;

use App\Common\Application\QueryHandler\ListQueryHandlerAbstract;
use App\Module\Company\Application\Query\Department\ListDepartmentsQuery;
use App\Module\Company\Domain\Entity\Department;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
final class ListDepartmentsQueryHandler extends ListQueryHandlerAbstract
{
    public function __invoke(ListDepartmentsQuery $query): array
    {
        return $this->handle($query);
    }

    public function getEntityClass(): string
    {
        return Department::class;
    }

    public function getAlias(): string
    {
        return Department::ALIAS;
    }

    public function getDefaultOrderBy(): string
    {
        return Department::COLUMN_CREATED_AT;
    }

    public function getAllowedFilters(): array
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

    public function getPhraseSearchColumns(): array
    {
        return [
            Department::COLUMN_NAME,
            Department::COLUMN_DESCRIPTION,
        ];
    }

    public function getRelations(): array
    {
        return Department::getRelations();
    }
}
