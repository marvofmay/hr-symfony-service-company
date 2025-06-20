<?php

declare(strict_types=1);

namespace App\Module\Company\Application\QueryHandler\Industry;

use App\Module\Company\Application\Query\Industry\GetIndustryByUUIDQuery;
use App\Module\Company\Application\Transformer\Industry\IndustryDataTransformer;
use App\Module\Company\Domain\Interface\Industry\IndustryReaderInterface;

final readonly class GetIndustryByUUIDQueryHandler
{
    public function __construct(private IndustryReaderInterface $industryReaderRepository)
    {
    }

    public function __invoke(GetIndustryByUUIDQuery $query): array
    {
        $industry = $this->industryReaderRepository->getIndustryByUUID($query->uuid);
        $transformer = new IndustryDataTransformer();

        return $transformer->transformToArray($industry);
    }
}
