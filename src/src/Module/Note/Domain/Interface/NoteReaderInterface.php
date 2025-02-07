<?php

declare(strict_types = 1);

namespace App\Module\Note\Domain\Interface;

use App\Module\Note\Domain\Entity\Note;

interface NoteReaderInterface
{
    public function getNoteByUUID(string $uuid): ?Note;
}
