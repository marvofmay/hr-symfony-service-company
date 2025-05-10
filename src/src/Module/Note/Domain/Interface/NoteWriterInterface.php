<?php

declare(strict_types=1);

namespace App\Module\Note\Domain\Interface;

use App\Module\Note\Domain\Entity\Note;

interface NoteWriterInterface
{
    public function saveNoteInDB(Note $note): void;
    public function deleteNoteInDB(Note $note): void;
}
