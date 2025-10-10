<?php

declare(strict_types=1);

namespace App\Module\Company\Application\QueryHandler\Industry;

use App\Common\Application\QueryHandler\ListQueryHandlerAbstract;
use App\Module\Company\Application\Event\Industry\IndustryListedEvent;
use App\Module\Company\Application\Query\Industry\ListIndustriesQuery;
use App\Module\Company\Domain\Entity\Industry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsMessageHandler(bus: 'query.bus')]
final class ListIndustriesQueryHandler extends ListQueryHandlerAbstract
{
    public function __construct(protected EntityManagerInterface $entityManager, private EventDispatcherInterface $eventDispatcher)
    {
        parent::__construct($entityManager);
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
        return Industry::COLUMN_CREATED_AT;
    }

    public function getAllowedFilters(): array
    {
        return [
            Industry::COLUMN_NAME,
            Industry::COLUMN_DESCRIPTION,
            Industry::COLUMN_CREATED_AT,
            Industry::COLUMN_UPDATED_AT,
            Industry::COLUMN_DELETED_AT,
        ];
    }

    public function getPhraseSearchColumns(): array
    {
        return [
            Industry::COLUMN_NAME,
            Industry::COLUMN_DESCRIPTION,
        ];
    }

    public function getRelations(): array
    {
        return Industry::getRelations();
    }
}
