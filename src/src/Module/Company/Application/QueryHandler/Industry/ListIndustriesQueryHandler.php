<?php

declare(strict_types=1);

namespace App\Module\Company\Application\QueryHandler\Industry;

use App\Common\Application\QueryHandler\ListQueryHandlerAbstract;
use App\Module\Company\Application\Query\Industry\ListIndustriesQuery;
use App\Module\Company\Domain\Entity\Industry;

class ListIndustriesQueryHandler extends ListQueryHandlerAbstract
{
    public function __invoke(ListIndustriesQuery $query): array
    {
        return $this->handle($query);
    }

    protected function getEntityClass(): string
    {
        return Industry::class;
    }

    protected function getAlias(): string
    {
        return Industry::ALIAS;
    }

    protected function getDefaultOrderBy(): string
    {
        return Industry::COLUMN_CREATED_AT;
    }

    protected function getAllowedFilters(): array
    {
        return [
            Industry::COLUMN_NAME,
            Industry::COLUMN_DESCRIPTION,
            Industry::COLUMN_CREATED_AT,
            Industry::COLUMN_UPDATED_AT,
            Industry::COLUMN_DELETED_AT,
        ];
    }

    protected function getPhraseSearchColumns(): array
    {
        return [
            Industry::COLUMN_NAME,
            Industry::COLUMN_DESCRIPTION,
        ];
    }

    protected function getRelations(): array
    {
        return Industry::getRelations();
    }
}
