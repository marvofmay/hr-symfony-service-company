<?php

declare(strict_types=1);

namespace App\Module\Note\Application\QueryHandler;

use App\Common\Application\QueryHandler\ListQueryHandlerAbstract;
use App\Module\Note\Application\Query\ListNotesQuery;
use App\Module\Note\Domain\Entity\Note;

class ListNotesQueryHandler extends ListQueryHandlerAbstract
{
    public function __invoke(ListNotesQuery $query): array
    {
        return $this->handle($query);
    }

    protected function getEntityClass(): string
    {
        return Note::class;
    }

    protected function getAlias(): string
    {
        return Note::ALIAS;
    }

    protected function getDefaultOrderBy(): string
    {
        return Note::COLUMN_CREATED_AT;
    }

    protected function getAllowedFilters(): array
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

    protected function getPhraseSearchColumns(): array
    {
        return [
            Note::COLUMN_TITLE,
            Note::COLUMN_CONTENT,
            Note::COLUMN_PRIORITY,
        ];
    }

    protected function getRelations(): array
    {
        return Note::getRelations();
    }
}