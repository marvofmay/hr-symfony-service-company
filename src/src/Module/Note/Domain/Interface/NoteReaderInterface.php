<?php

declare(strict_types=1);

namespace App\Module\Note\Domain\Interface;

use App\Module\Company\Domain\Entity\Employee;
use App\Module\Note\Domain\Entity\Note;
use Doctrine\Common\Collections\Collection;

interface NoteReaderInterface
{
    public function getNoteByUUID(string $uuid): ?Note;
    public function getNotesByUUIDsAndEmployee(array $uuids, ?Employee $employee): Collection;
    public function getNoteByUUIDAndEmployee(string $uuid, ?Employee $employee): ?Note;

    public function isNoteWithUUIDExists(string $uuid): bool;
    public function isNoteWithUUIDAndEmployeeExists(string $uuid, ?Employee $employee): bool;
}
