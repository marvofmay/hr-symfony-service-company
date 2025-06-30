<?php

declare(strict_types=1);

namespace App\Module\Company\Application\QueryHandler\Position;;

use App\Common\Application\QueryHandler\ListQueryHandlerAbstract;
use App\Module\Company\Application\Event\Position\PositionListedEvent;
use App\Module\Company\Application\Query\Position\ListPositionsQuery;
use App\Module\Company\Domain\Entity\Position;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsMessageHandler(bus: 'query.bus')]
final class ListPositionsQueryHandler extends ListQueryHandlerAbstract
{
    public function __construct(protected EntityManagerInterface $entityManager, private EventDispatcherInterface $eventDispatcher,)
    {
        parent::__construct($entityManager);
    }
    public function __invoke(ListPositionsQuery $query): array
    {
        $this->eventDispatcher->dispatch(new PositionListedEvent([$query]));
        return $this->handle($query);
    }

    public function getEntityClass(): string
    {
        return Position::class;
    }

    public function getAlias(): string
    {
        return Position::ALIAS;
    }

    public function getDefaultOrderBy(): string
    {
        return Position::COLUMN_CREATED_AT;
    }

    public function getAllowedFilters(): array
    {
        return [
            Position::COLUMN_NAME,
            Position::COLUMN_DESCRIPTION,
            Position::COLUMN_CREATED_AT,
            Position::COLUMN_UPDATED_AT,
            Position::COLUMN_DELETED_AT,
        ];
    }

    public function getPhraseSearchColumns(): array
    {
        return [
            Position::COLUMN_NAME,
            Position::COLUMN_DESCRIPTION,
        ];
    }

    public function getRelations(): array
    {
        return Position::getRelations();
    }
}
