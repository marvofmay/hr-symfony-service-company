<?php

declare(strict_types=1);

namespace App\Module\Note\Application\CommandHandler;

use App\Module\Note\Application\Command\UpdateNoteCommand;
use App\Module\Note\Domain\Service\NoteService;

readonly class UpdateNoteCommandHandler
{
    public function __construct(private NoteService $noteWriterService)
    {
    }

    public function __invoke(UpdateNoteCommand $command): void
    {
        $note = $command->getNote();
        $note->setTitle($command->getTitle());
        $note->setContent($command->getContent());
        $note->setPriority($command->getPriority());
        $note->setUpdatedAt(new \DateTime());

        $this->noteWriterService->updateNoteInDB($note);
    }
}
