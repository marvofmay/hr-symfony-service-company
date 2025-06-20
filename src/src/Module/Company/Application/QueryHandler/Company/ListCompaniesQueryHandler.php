<?php

declare(strict_types=1);

namespace App\Module\Company\Application\QueryHandler\Company;

use App\Common\Application\QueryHandler\ListQueryHandlerAbstract;
use App\Module\Company\Application\Query\Company\ListCompaniesQuery;
use App\Module\Company\Domain\Entity\Company;

class ListCompaniesQueryHandler extends ListQueryHandlerAbstract
{
    public function __invoke(ListCompaniesQuery $query): array
    {
        return $this->handle($query);
    }

    protected function getEntityClass(): string
    {
        return Company::class;
    }

    protected function getAlias(): string
    {
        return Company::ALIAS;
    }

    protected function getDefaultOrderBy(): string
    {
        return Company::COLUMN_CREATED_AT;
    }

    protected function getAllowedFilters(): array
    {
        return [
            Company::COLUMN_FULL_NAME,
            Company::COLUMN_SHORT_NAME,
            Company::COLUMN_DESCRIPTION,
            Company::COLUMN_NIP,
            Company::COLUMN_REGON,
            Company::COLUMN_ACTIVE,
            Company::COLUMN_CREATED_AT,
            Company::COLUMN_UPDATED_AT,
            Company::COLUMN_DELETED_AT,
        ];
    }

    protected function getPhraseSearchColumns(): array
    {
        return [
            Company::COLUMN_FULL_NAME,
            Company::COLUMN_SHORT_NAME,
            Company::COLUMN_DESCRIPTION,
            Company::COLUMN_NIP,
            Company::COLUMN_REGON,
        ];
    }

    protected function getRelations(): array
    {
        return Company::getRelations();
    }
}
