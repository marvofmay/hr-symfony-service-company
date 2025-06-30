<?php

declare(strict_types=1);

namespace App\Module\Note\Application\QueryHandler;

use App\Common\Application\QueryHandler\ListQueryHandlerAbstract;
use App\Module\Note\Application\Query\ListNotesQuery;
use App\Module\Note\Domain\Entity\Note;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
final class ListNotesQueryHandler extends ListQueryHandlerAbstract
{
    public function __invoke(ListNotesQuery $query): array
    {
        return $this->handle($query);
    }

    public function getEntityClass(): string
    {
        return Note::class;
    }

    public function getAlias(): string
    {
        return Note::ALIAS;
    }

    public function getDefaultOrderBy(): string
    {
        return Note::COLUMN_CREATED_AT;
    }

    public function getAllowedFilters(): array
    {
        return [
            Note::COLUMN_TITLE,
            Note::COLUMN_CONTENT,
            Note::COLUMN_PRIORITY,
            Note::COLUMN_CREATED_AT,
            Note::COLUMN_UPDATED_AT,
            Note::COLUMN_DELETED_AT,
        ];
    }

    public function getPhraseSearchColumns(): array
    {
        return [
            Note::COLUMN_TITLE,
            Note::COLUMN_CONTENT,
            Note::COLUMN_PRIORITY,
        ];
    }

    public function getRelations(): array
    {
        return Note::getRelations();
    }
}
