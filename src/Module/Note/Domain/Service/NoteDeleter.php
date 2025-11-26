<?php

declare(strict_types=1);

namespace App\Module\Note\Domain\Service;

use App\Module\Note\Domain\Entity\Note;
use App\Module\Note\Domain\Interface\NoteWriterInterface;

readonly class NoteDeleter
{
    public function __construct(private NoteWriterInterface $noteWriterRepository)
    {
    }

    public function delete(Note $note): void
    {
        $this->noteWriterRepository->delete($note);
    }
}
