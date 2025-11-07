<?php

declare(strict_types=1);

namespace App\Module\Note\Domain\Service;

use App\Module\Note\Domain\Entity\Note;
use App\Module\Note\Domain\Enum\NotePriorityEnum;
use App\Module\Note\Domain\Interface\NoteWriterInterface;

readonly class NoteUpdater
{
    public function __construct(private NoteWriterInterface $noteWriterRepository)
    {
    }

    public function update(Note $note, ?string $title, ?string $content, NotePriorityEnum $priority): void
    {


        $this->noteWriterRepository->save($note);
    }
}
