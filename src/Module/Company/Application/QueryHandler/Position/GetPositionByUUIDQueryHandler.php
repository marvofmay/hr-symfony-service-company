<?php

declare(strict_types=1);

namespace App\Module\Company\Application\QueryHandler\Position;

use App\Common\Domain\Interface\QueryInterface;
use App\Module\Company\Application\Event\Position\PositionViewedEvent;
use App\Module\Company\Application\Query\Position\GetPositionByUUIDQuery;
use App\Module\Company\Application\Transformer\Position\PositionDataTransformer;
use App\Module\Company\Domain\Interface\Position\PositionReaderInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsMessageHandler(bus: 'query.bus')]
final readonly class GetPositionByUUIDQueryHandler
{
    public function __construct(
        private PositionReaderInterface $positionReaderRepository,
        private EventDispatcherInterface $eventDispatcher,
        #[AutowireIterator(tag: 'app.position.query.get.validator')] protected iterable $validators,
    ) {
    }

    public function __invoke(GetPositionByUUIDQuery $query): array
    {
        $this->validate($query);

        $position = $this->positionReaderRepository->getPositionByUUID($query->positionUUID);
        $transformer = new PositionDataTransformer();

        $this->eventDispatcher->dispatch(new PositionViewedEvent([
            GetPositionByUUIDQuery::POSITION_UUID => $query->positionUUID,
        ]));

        return $transformer->transformToArray($position);
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
