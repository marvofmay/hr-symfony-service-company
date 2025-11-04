<?php

declare(strict_types=1);

namespace App\Module\Company\Application\QueryHandler\ContractType;

use App\Common\Domain\Interface\QueryInterface;
use App\Module\Company\Application\Event\ContractType\ContractTypeViewedEvent;
use App\Module\Company\Application\Query\ContractType\GetContractTypeByUUIDQuery;
use App\Module\Company\Application\Transformer\ContractType\ContractTypeDataTransformer;
use App\Module\Company\Domain\Interface\ContractType\ContractTypeReaderInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsMessageHandler(bus: 'query.bus')]
final readonly class GetContractTypeByUUIDQueryHandler
{
    public function __construct(
        private ContractTypeReaderInterface $contractTypeReaderRepository,
        private EventDispatcherInterface $eventDispatcher,
        #[AutowireIterator(tag: 'app.contract_type.query.get.validator')] protected iterable $validators,
    )
    {
    }

    public function __invoke(GetContractTypeByUUIDQuery $query): array
    {
        $this->validate($query);

        $contractType = $this->contractTypeReaderRepository->getContractTypeByUUID($query->contractTypeUUID);
        $transformer = new ContractTypeDataTransformer();

        $this->eventDispatcher->dispatch(new ContractTypeViewedEvent([
            GetContractTypeByUUIDQuery::CONTRACT_TYPE_UUID => $query->contractTypeUUID,
        ]));

        return $transformer->transformToArray($contractType);
    }

    private function validate(QueryInterface $query): void
    {
        foreach ($this->validators as $validator) {
            if (method_exists($validator, 'supports') && $validator->supports($query)) {
                $validator->validate($query);
            }
        }
    }
}
