<?php

declare(strict_types=1);

namespace App\Module\Company\Application\QueryHandler\ContractType;

use App\Common\Application\Factory\TransformerFactory;
use App\Common\Application\QueryHandler\ListQueryHandlerAbstract;
use App\Module\Company\Application\Event\ContractType\ContractTypeListedEvent;
use App\Module\Company\Application\Query\ContractType\ListContractTypesQuery;
use App\Module\Company\Domain\Entity\ContractType;
use App\Module\Company\Domain\Enum\ContractType\ContractTypeEntityFieldEnum;
use App\Module\Company\Domain\Enum\TimeStampableEntityFieldEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsMessageHandler(bus: 'query.bus')]
final class ListContractTypesQueryHandler extends ListQueryHandlerAbstract
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected TransformerFactory $transformerFactory,
        private readonly EventDispatcherInterface $eventDispatcher
    )
    {
        parent::__construct($entityManager, $transformerFactory);
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
        return TimeStampableEntityFieldEnum::CREATED_AT->value;
    }

    public function getAllowedFilters(): array
    {
        return [
            ContractTypeEntityFieldEnum::NAME->value,
            ContractTypeEntityFieldEnum::DESCRIPTION->value,
            ContractTypeEntityFieldEnum::ACTIVE->value,
            TimeStampableEntityFieldEnum::CREATED_AT->value,
            TimeStampableEntityFieldEnum::UPDATED_AT->value,
            TimeStampableEntityFieldEnum::DELETED_AT->value,
        ];
    }

    public function getPhraseSearchColumns(): array
    {
        return [
            ContractTypeEntityFieldEnum::NAME->value,
            ContractTypeEntityFieldEnum::DESCRIPTION->value,
        ];
    }

    public function getRelations(): array
    {
        return ContractType::getRelations();
    }
}
