<?php

declare(strict_types=1);

namespace App\Module\Company\Application\QueryHandler\Industry;

use App\Common\Domain\Interface\QueryInterface;
use App\Module\Company\Application\Event\Industry\IndustryViewedEvent;
use App\Module\Company\Application\Query\Industry\GetIndustryByUUIDQuery;
use App\Module\Company\Application\Transformer\Industry\IndustryDataTransformer;
use App\Module\Company\Domain\Entity\Industry;
use App\Module\Company\Domain\Interface\Industry\IndustryReaderInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsMessageHandler(bus: 'query.bus')]
final readonly class GetIndustryByUUIDQueryHandler
{
    public function __construct(
        private IndustryReaderInterface $industryReaderRepository,
        private EventDispatcherInterface $eventDispatcher,
        #[AutowireIterator(tag: 'app.industry.query.get.validator')] protected iterable $validators,
    )
    {
    }

    public function __invoke(GetIndustryByUUIDQuery $query): array
    {
        $this->validate($query);

        $industry = $this->industryReaderRepository->getIndustryByUUID($query->industryUUID);
        $transformer = new IndustryDataTransformer();

        $this->eventDispatcher->dispatch(new IndustryViewedEvent([
            Industry::COLUMN_UUID => $query->industryUUID,
        ]));

        return $transformer->transformToArray($industry);
    }

    protected function validate(QueryInterface $query): void
    {
        foreach ($this->validators as $validator) {
            if (method_exists($validator, 'supports') && $validator->supports($query)) {
                $validator->validate($query);
            }
        }
    }
}
