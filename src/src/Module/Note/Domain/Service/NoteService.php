<?php

declare(strict_types = 1);

namespace App\Module\Note\Domain\Service;

use App\Module\Note\Domain\Entity\Note;
use App\Module\Note\Domain\Interface\NoteWriterInterface;

readonly class NoteService
{
    public function __construct(private NoteWriterInterface $noteWriterRepository)
    {
    }

    public function __toString()
    {
        return 'NoteService';
    }

    public function saveNoteInDB(Note $note): void
    {
        $this->noteWriterRepository->saveNoteInDB($note);
    }

    public function updateNoteInDB(Note $note): void
    {
        $this->noteWriterRepository->updateNoteInDB($note);
    }
}