<?php

declare(strict_types=1);

namespace App\Module\Company\Application\QueryHandler\Company;

use App\Module\Company\Application\Query\Company\GetCompanySelectOptionsQuery;
use App\Module\Company\Domain\Interface\Company\CompanyReaderInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
final readonly class GetCompanySelectOptionsQueryHandler
{
    public function __construct(
        private CompanyReaderInterface $companyReaderRepository,
    ) {
    }

    public function __invoke(GetCompanySelectOptionsQuery $query): array
    {
        return $this->companyReaderRepository->getSelectOptions();
    }
}
