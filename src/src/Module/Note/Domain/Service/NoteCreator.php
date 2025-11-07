<?php

declare(strict_types=1);

namespace App\Module\Note\Domain\Service;

use App\Module\Company\Domain\Entity\Employee;
use App\Module\Note\Domain\Entity\Note;
use App\Module\Note\Domain\Enum\NotePriorityEnum;
use App\Module\Note\Domain\Interface\NoteCreatorInterface;
use App\Module\Note\Domain\Interface\NoteWriterInterface;

final readonly class NoteCreator implements NoteCreatorInterface
{
    public function __construct(private NoteWriterInterface $noteWriterRepository)
    {
    }

    public function create(string $title, ?string $content = null, NotePriorityEnum $priority = NotePriorityEnum::LOW, ?Employee $employee = null): void
    {
        $note = Note::create($title, $content, $priority, $employee);

        $this->noteWriterRepository->save($note);
    }
}
