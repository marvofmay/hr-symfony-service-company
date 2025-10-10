<?php

declare(strict_types=1);

namespace App\Module\Company\Application\QueryHandler\ContractType;

use App\Module\Company\Application\Event\ContractType\ContractTypeViewedEvent;
use App\Module\Company\Application\Query\ContractType\GetContractTypeByUUIDQuery;
use App\Module\Company\Application\Transformer\ContractType\ContractTypeDataTransformer;
use App\Module\Company\Domain\Entity\ContractType;
use App\Module\Company\Domain\Interface\ContractType\ContractTypeReaderInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsMessageHandler(bus: 'query.bus')]
final readonly class GetContractTypeByUUIDQueryHandler
{
    public function __construct(private ContractTypeReaderInterface $contractTypeReaderRepository, private EventDispatcherInterface $eventDispatcher)
    {
    }

    public function __invoke(GetContractTypeByUUIDQuery $query): array
    {
        $contractType = $this->contractTypeReaderRepository->getContractTypeByUUID($query->uuid);
        $transformer = new ContractTypeDataTransformer();

        $this->eventDispatcher->dispatch(new ContractTypeViewedEvent([
            ContractType::COLUMN_UUID => $query->uuid,
        ]));

        return $transformer->transformToArray($contractType);
    }
}
