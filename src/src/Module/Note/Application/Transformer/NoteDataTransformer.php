<?php

declare(strict_types=1);

namespace App\Module\Note\Application\Transformer;

use App\Module\Company\Domain\Entity\Employee;
use App\Module\Company\Domain\Enum\TimeStampableEntityFieldEnum;
use App\Module\Note\Domain\Entity\Note;
use App\Module\Note\Domain\Enum\NoteEntityFieldEnum;
use App\Module\Note\Domain\Enum\NoteEntityRelationFieldEnum;

class NoteDataTransformer
{
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
            NoteEntityRelationFieldEnum::EMPLOYEE->value => $note->getEmployee() ? $this->transformEmployee($note->getEmployee()) : [],
            default => null,
        };
    }

    private function transformEmployee(Employee $employee): ?array
    {
        return [
            Employee::COLUMN_UUID => $employee->getUUID()->toString(),
            Employee::COLUMN_FIRST_NAME => $employee->getFirstName(),
            Employee::COLUMN_LAST_NAME => $employee->getLastName(),
            Employee::COLUMN_CREATED_AT => $employee->getCreatedAt()?->format('Y-m-d H:i:s'),
            Employee::COLUMN_UPDATED_AT => $employee->getUpdatedAt()?->format('Y-m-d H:i:s'),
            Employee::COLUMN_DELETED_AT => $employee->getDeletedAt()?->format('Y-m-d H:i:s'),
        ];
    }
}
