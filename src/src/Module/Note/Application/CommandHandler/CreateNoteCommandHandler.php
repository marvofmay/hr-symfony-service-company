<?php

declare(strict_types=1);

namespace App\Module\Note\Application\CommandHandler;

use App\Module\Note\Application\Command\CreateNoteCommand;
use App\Module\Note\Domain\Entity\Note;
use App\Module\Note\Domain\Service\NoteService;

readonly class CreateNoteCommandHandler
{
    public function __construct(private NoteService $noteService)
    {
    }

    public function __invoke(CreateNoteCommand $command): void
    {
        $note = new Note();
        //$employee =
        $note->setEmployee($employee);
        $note->setTitle($command->title);
        $note->setContent($command->content);
        $note->setPriority($command->priority);

        $this->noteService->saveNoteInDB($note);
    }
}
