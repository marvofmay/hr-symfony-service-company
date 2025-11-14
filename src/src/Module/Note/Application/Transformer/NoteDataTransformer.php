<?php

declare(strict_types=1);

namespace App\Module\Note\Application\Transformer;

use App\Common\Domain\Enum\TimeStampableEntityFieldEnum;
use App\Common\Domain\Interface\DataTransformerInterface;
use App\Module\Company\Domain\Entity\User;
use App\Module\Note\Application\QueryHandler\ListNotesQueryHandler;
use App\Module\Note\Domain\Entity\Note;
use App\Module\Note\Domain\Enum\NoteEntityFieldEnum;
use App\Module\Note\Domain\Enum\NoteEntityRelationFieldEnum;
use Symfony\Component\Security\Core\User\UserInterface;

class NoteDataTransformer implements DataTransformerInterface
{
    public static function supports(): string
    {
        return ListNotesQueryHandler::class;
    }

    public function transformToArray(Note $note, array $includes = []): array
    {
        $data = [
            NoteEntityFieldEnum::UUID->value => $note->getUUID()->toString(),
            NoteEntityFieldEnum::TITLE->value => $note->getTitle(),
            NoteEntityFieldEnum::CONTENT->value => $note->getContent(),
            NoteEntityFieldEnum::PRIORITY->value => $note->getPriority(),
            TimeStampableEntityFieldEnum::CREATED_AT->value => $note->createdAt->format('Y-m-d H:i:s'),
            TimeStampableEntityFieldEnum::UPDATED_AT->value => $note->updatedAt?->format('Y-m-d H:i:s'),
            TimeStampableEntityFieldEnum::DELETED_AT->value => $note->deletedAt?->format('Y-m-d H:i:s'),
        ];

        foreach ($includes as $relation) {
            if (in_array($relation, Note::getRelations(), true)) {
                $data[$relation] = $this->transformRelation($note, $relation);
            }
        }

        return $data;
    }

    private function transformRelation(Note $note, string $relation): ?array
    {
        return match ($relation) {
            NoteEntityRelationFieldEnum::USER->value => $this->transformUser($note->getUser()),
            default => null,
        };
    }

    private function transformUser(UserInterface $user): ?array
    {
        return [
            User::COLUMN_UUID => $user->getUUID()->toString(),
            User::COLUMN_EMAIL => $user->getEmail(),
        ];
    }
}
