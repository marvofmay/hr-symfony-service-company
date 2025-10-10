<?php

declare(strict_types=1);

namespace App\Module\Company\Application\QueryHandler\Industry;

use App\Module\Company\Application\Event\Industry\IndustryViewedEvent;
use App\Module\Company\Application\Query\Industry\GetIndustryByUUIDQuery;
use App\Module\Company\Application\Transformer\Industry\IndustryDataTransformer;
use App\Module\Company\Domain\Entity\Industry;
use App\Module\Company\Domain\Interface\Industry\IndustryReaderInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final readonly class GetIndustryByUUIDQueryHandler
{
    public function __construct(private IndustryReaderInterface $industryReaderRepository, private EventDispatcherInterface $eventDispatcher)
    {
    }

    public function __invoke(GetIndustryByUUIDQuery $query): array
    {
        $industry = $this->industryReaderRepository->getIndustryByUUID($query->uuid);
        $transformer = new IndustryDataTransformer();

        $this->eventDispatcher->dispatch(new IndustryViewedEvent([
            Industry::COLUMN_UUID => $query->uuid,
        ]));

        return $transformer->transformToArray($industry);
    }
}
