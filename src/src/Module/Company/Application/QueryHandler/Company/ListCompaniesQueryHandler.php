<?php

declare(strict_types=1);

namespace App\Module\Company\Application\QueryHandler\Company;

use App\Common\Application\QueryHandler\ListQueryHandlerAbstract;
use App\Module\Company\Application\Query\Company\ListCompaniesQuery;
use App\Module\Company\Domain\Entity\Company;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
final class ListCompaniesQueryHandler extends ListQueryHandlerAbstract
{
    public function __invoke(ListCompaniesQuery $query): array
    {
        return $this->handle($query);
    }

    public function getEntityClass(): string
    {
        return Company::class;
    }

    public function getAlias(): string
    {
        return Company::ALIAS;
    }

    public function getDefaultOrderBy(): string
    {
        return Company::COLUMN_CREATED_AT;
    }

    public function getAllowedFilters(): array
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

    public function getPhraseSearchColumns(): array
    {
        return [
            Company::COLUMN_FULL_NAME,
            Company::COLUMN_SHORT_NAME,
            Company::COLUMN_DESCRIPTION,
            Company::COLUMN_NIP,
            Company::COLUMN_REGON,
        ];
    }

    public function getRelations(): array
    {
        return Company::getRelations();
    }
}
