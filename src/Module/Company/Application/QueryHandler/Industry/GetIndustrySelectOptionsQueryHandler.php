<?php

declare(strict_types=1);

namespace App\Module\Company\Application\QueryHandler\Industry;

use App\Module\Company\Application\Query\Industry\GetIndustrySelectOptionsQuery;
use App\Module\Company\Domain\Interface\Industry\IndustryReaderInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
final readonly class GetIndustrySelectOptionsQueryHandler
{
    public function __construct(
        private IndustryReaderInterface $roleReaderRepository,
    ) {
    }

    public function __invoke(GetIndustrySelectOptionsQuery $query): array
    {
        return $this->roleReaderRepository->getSelectOptions();
    }
}
