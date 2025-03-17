<?php

declare(strict_types=1);

namespace App\Module\Note\Application\Transformer;

use App\Module\Company\Domain\Entity\Employee;
use App\Module\Note\Domain\Entity\Note;

class NoteDataTransformer
{
    public function transformToArray(Note $note, array $includes = []): array
    {
        $data = [
            Note::COLUMN_UUID => $note->getUUID()->toString(),
            Note::COLUMN_TITLE => $note->getTitle(),
            Note::COLUMN_CONTENT => $note->getContent(),
            Note::COLUMN_PRIORITY => $note->getPriority(),
            Note::COLUMN_CREATED_AT => $note->getCreatedAt()?->format('Y-m-d H:i:s'),
            Note::COLUMN_UPDATED_AT => $note->getUpdatedAt()?->format('Y-m-d H:i:s'),
            Note::COLUMN_DELETED_AT => $note->getDeletedAt()?->format('Y-m-d H:i:s'),
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
            Note::RELATION_EMPLOYEE => $this->transformEmployee($note->getEmployee()),
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