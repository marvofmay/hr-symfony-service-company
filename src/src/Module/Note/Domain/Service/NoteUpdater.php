<?php

declare(strict_types=1);

namespace App\Module\Note\Domain\Service;

use App\Module\Note\Domain\Entity\Note;
use App\Module\Note\Domain\Enum\NotePriorityEnum;
use App\Module\Note\Domain\Interface\NoteUpdaterInterface;
use App\Module\Note\Domain\Interface\NoteWriterInterface;

readonly class NoteUpdater implements NoteUpdaterInterface
{
    public function __construct(private NoteWriterInterface $noteWriterRepository)
    {
    }

    public function update(Note $note, string $title, ?string $content = null, NotePriorityEnum $priority = NotePriorityEnum::LOW): void
    {
        $note->changeTitle($title);
        if (null !== $content) {
            $note->changeContent($content);
        }
        $note->changePriority($priority);

        $this->noteWriterRepository->save($note);
    }
}
