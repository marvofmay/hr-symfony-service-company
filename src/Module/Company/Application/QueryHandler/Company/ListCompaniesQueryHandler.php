<?php

declare(strict_types=1);

namespace App\Module\Company\Application\QueryHandler\Company;

use App\Common\Application\Factory\TransformerFactory;
use App\Common\Application\QueryHandler\ListQueryHandlerAbstract;
use App\Module\Company\Application\Event\Company\CompanyListedEvent;
use App\Module\Company\Application\Query\Company\ListCompaniesQuery;
use App\Module\Company\Domain\Entity\Company;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsMessageHandler(bus: 'query.bus')]
final class ListCompaniesQueryHandler extends ListQueryHandlerAbstract
{
    public function __construct(
        public EntityManagerInterface $entityManager,
        protected TransformerFactory $transformerFactory,
        private readonly EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct($entityManager, $transformerFactory);
    }

    public function __invoke(ListCompaniesQuery $query): array
    {
        $this->eventDispatcher->dispatch(new CompanyListedEvent([$query]));

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
            Company::COLUMN_INTERNAL_CODE,
            Company::COLUMN_DESCRIPTION,
            Company::COLUMN_NIP,
            Company::COLUMN_REGON,
            Company::COLUMN_ACTIVE,
            Company::COLUMN_CREATED_AT,
            Company::COLUMN_UPDATED_AT,
            Company::COLUMN_DELETED_AT,
            Company::RELATION_PARENT_COMPANY,
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
