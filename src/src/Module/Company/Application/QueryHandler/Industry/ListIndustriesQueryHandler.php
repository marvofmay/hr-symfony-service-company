<?php

declare(strict_types=1);

namespace App\Module\Company\Application\QueryHandler\Industry;

use App\Common\Application\Factory\TransformerFactory;
use App\Common\Application\QueryHandler\ListQueryHandlerAbstract;
use App\Common\Domain\Enum\TimeStampableEntityFieldEnum;
use App\Module\Company\Application\Event\Industry\IndustryListedEvent;
use App\Module\Company\Application\Query\Industry\ListIndustriesQuery;
use App\Module\Company\Domain\Entity\Industry;
use App\Module\Company\Domain\Enum\Industry\IndustryEntityFieldEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsMessageHandler(bus: 'query.bus')]
final class ListIndustriesQueryHandler extends ListQueryHandlerAbstract
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected TransformerFactory $transformerFactory,
        private EventDispatcherInterface $eventDispatcher
    )
    {
        parent::__construct($entityManager, $transformerFactory);
    }

    public function __invoke(ListIndustriesQuery $query): array
    {
        $this->eventDispatcher->dispatch(new IndustryListedEvent([$query]));

        return $this->handle($query);
    }

    public function getEntityClass(): string
    {
        return Industry::class;
    }

    public function getAlias(): string
    {
        return Industry::ALIAS;
    }

    public function getDefaultOrderBy(): string
    {
        return TimeStampableEntityFieldEnum::CREATED_AT->value;
    }

    public function getAllowedFilters(): array
    {
        return [
            IndustryEntityFieldEnum::NAME->value,
            IndustryEntityFieldEnum::DESCRIPTION->value,
            TimestampableEntityFieldEnum::CREATED_AT->value,
            TimeStampableEntityFieldEnum::UPDATED_AT->value,
            TimestampableEntityFieldEnum::DELETED_AT->value,
        ];
    }

    public function getPhraseSearchColumns(): array
    {
        return [
            IndustryEntityFieldEnum::NAME->value,
            IndustryEntityFieldEnum::DESCRIPTION->value,
        ];
    }

    public function getRelations(): array
    {
        return Industry::getRelations();
    }
}
