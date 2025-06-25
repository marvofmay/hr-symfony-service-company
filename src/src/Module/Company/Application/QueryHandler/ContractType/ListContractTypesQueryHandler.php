<?php

declare(strict_types=1);

namespace App\Module\Company\Application\QueryHandler\ContractType;

use App\Common\Application\QueryHandler\ListQueryHandlerAbstract;
use App\Module\Company\Application\Event\ContractType\ContractTypeListedEvent;
use App\Module\Company\Application\Query\ContractType\ListContractTypesQuery;
use App\Module\Company\Domain\Entity\ContractType;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManagerInterface;

#[AsMessageHandler(bus: 'query.bus')]
class ListContractTypesQueryHandler extends ListQueryHandlerAbstract
{
    public function __construct(protected EntityManagerInterface $entityManager, private EventDispatcherInterface $eventDispatcher,)
    {
        parent::__construct($entityManager);
    }

    public function __invoke(ListContractTypesQuery $query): array
    {
        $this->eventDispatcher->dispatch(new ContractTypeListedEvent([$query]));

        return $this->handle($query);
    }

    protected function getEntityClass(): string
    {
        return ContractType::class;
    }

    protected function getAlias(): string
    {
        return ContractType::ALIAS;
    }

    protected function getDefaultOrderBy(): string
    {
        return ContractType::COLUMN_CREATED_AT;
    }

    protected function getAllowedFilters(): array
    {
        return [
            ContractType::COLUMN_NAME,
            ContractType::COLUMN_DESCRIPTION,
            ContractType::COLUMN_ACTIVE,
            ContractType::COLUMN_CREATED_AT,
            ContractType::COLUMN_UPDATED_AT,
            ContractType::COLUMN_DELETED_AT,
        ];
    }

    protected function getPhraseSearchColumns(): array
    {
        return [
            ContractType::COLUMN_NAME,
            ContractType::COLUMN_DESCRIPTION,
        ];
    }

    protected function getRelations(): array
    {
        return ContractType::getRelations();
    }
}
