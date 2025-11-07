<?php

declare(strict_types=1);

namespace App\Module\Note\Application\QueryHandler;

use App\Common\Application\QueryHandler\ListQueryHandlerAbstract;
use App\Module\Company\Domain\Enum\TimeStampableEntityFieldEnum;
use App\Module\Note\Application\Query\ListNotesQuery;
use App\Module\Note\Domain\Entity\Note;
use App\Module\Note\Domain\Enum\NoteEntityFieldEnum;
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
        return TimeStampableEntityFieldEnum::CREATED_AT->value;
    }

    public function getAllowedFilters(): array
    {
        return [
            NoteEntityFieldEnum::TITLE->value,
            NoteEntityFieldEnum::CONTENT->value,
            NoteEntityFieldEnum::PRIORITY->value,
            TimeStampableEntityFieldEnum::CREATED_AT->value,
            TimeStampableEntityFieldEnum::UPDATED_AT->value,
            TimeStampableEntityFieldEnum::DELETED_AT->value,
        ];
    }

    public function getPhraseSearchColumns(): array
    {
        return [
            NoteEntityFieldEnum::TITLE->value,
            NoteEntityFieldEnum::CONTENT->value,
            NoteEntityFieldEnum::PRIORITY->value,
        ];
    }

    public function getRelations(): array
    {
        return Note::getRelations();
    }
}
