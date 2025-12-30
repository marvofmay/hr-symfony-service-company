<?php

declare(strict_types=1);

namespace App\Module\Company\Application\QueryHandler\ContractType;

use App\Module\Company\Application\Query\ContractType\GetContractTypeSelectOptionsQuery;
use App\Module\Company\Domain\Interface\ContractType\ContractTypeReaderInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
final readonly class GetContractTypeSelectOptionsQueryHandler
{
    public function __construct(private ContractTypeReaderInterface $contractTypeReaderRepository)
    {
    }

    public function __invoke(GetContractTypeSelectOptionsQuery $query): array
    {
        return $this->contractTypeReaderRepository->getSelectOptions();
    }
}
