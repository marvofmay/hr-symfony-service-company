<?php

declare(strict_types=1);

namespace App\Module\Company\Application\QueryHandler\ContractType;

use App\Common\Application\QueryHandler\ListQueryHandlerAbstract;
use App\Module\Company\Application\Event\ContractType\ContractTypeListedEvent;
use App\Module\Company\Application\Query\ContractType\ListContractTypesQuery;
use App\Module\Company\Domain\Entity\ContractType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsMessageHandler(bus: 'query.bus')]
final class ListContractTypesQueryHandler extends ListQueryHandlerAbstract
{
    public function __construct(protected EntityManagerInterface $entityManager, private EventDispatcherInterface $eventDispatcher)
    {
        parent::__construct($entityManager);
    }

    public function __invoke(ListContractTypesQuery $query): array
    {
        $this->eventDispatcher->dispatch(new ContractTypeListedEvent([$query]));

        return $this->handle($query);
    }

    public function getEntityClass(): string
    {
        return ContractType::class;
    }

    public function getAlias(): string
    {
        return ContractType::ALIAS;
    }

    public function getDefaultOrderBy(): string
    {
        return ContractType::COLUMN_CREATED_AT;
    }

    public function getAllowedFilters(): array
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

    public function getPhraseSearchColumns(): array
    {
        return [
            ContractType::COLUMN_NAME,
            ContractType::COLUMN_DESCRIPTION,
        ];
    }

    public function getRelations(): array
    {
        return ContractType::getRelations();
    }
}
