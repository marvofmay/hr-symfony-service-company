<?php

declare(strict_types=1);

namespace App\Module\Company\Application\QueryHandler\Position;

use App\Module\Company\Application\Query\Position\GetPositionByUUIDQuery;
use App\Module\Company\Application\Transformer\Position\PositionDataTransformer;
use App\Module\Company\Domain\Interface\Position\PositionReaderInterface;

final readonly class GetPositionByUUIDQueryHandler
{
    public function __construct(private PositionReaderInterface $positionReaderRepository)
    {
    }

    public function __invoke(GetPositionByUUIDQuery $query): array
    {
        $position = $this->positionReaderRepository->getPositionByUUID($query->uuid);
        $transformer = new PositionDataTransformer();

        return $transformer->transformToArray($position);
    }
}
