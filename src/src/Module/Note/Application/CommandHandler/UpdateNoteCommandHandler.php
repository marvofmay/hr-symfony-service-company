<?php

declare(strict_types=1);

namespace App\Module\Note\Application\CommandHandler;

use App\Module\Note\Application\Command\UpdateNoteCommand;
use App\Module\Note\Domain\Interface\NoteWriterInterface;

readonly class UpdateNoteCommandHandler
{
    public function __construct(private NoteWriterInterface $noteWriterRepository,)
    {
    }

    public function __invoke(UpdateNoteCommand $command): void
    {
        $note = $command->getNote();
        $note->setTitle($command->getTitle());
        $note->setContent($command->getContent());
        $note->setPriority($command->getPriority());
        $note->setUpdatedAt(new \DateTime());

        $this->noteWriterRepository->updateNoteInDB($note);
    }
}
