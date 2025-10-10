<?php

declare(strict_types=1);

namespace App\Module\Company\Application\QueryHandler\Position;

use App\Module\Company\Application\Event\Position\PositionViewedEvent;
use App\Module\Company\Application\Query\Position\GetPositionByUUIDQuery;
use App\Module\Company\Application\Transformer\Position\PositionDataTransformer;
use App\Module\Company\Domain\Entity\Position;
use App\Module\Company\Domain\Interface\Position\PositionReaderInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final readonly class GetPositionByUUIDQueryHandler
{
    public function __construct(private PositionReaderInterface $positionReaderRepository, private EventDispatcherInterface $eventDispatcher)
    {
    }

    public function __invoke(GetPositionByUUIDQuery $query): array
    {
        $position = $this->positionReaderRepository->getPositionByUUID($query->uuid);
        $transformer = new PositionDataTransformer();

        $this->eventDispatcher->dispatch(new PositionViewedEvent([
            Position::COLUMN_UUID => $query->uuid,
        ]));

        return $transformer->transformToArray($position);
    }
}
