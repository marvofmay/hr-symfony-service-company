<?php

namespace App\Module\Note\Application\CommandHandler;

use App\Module\Note\Application\Command\DeleteNoteCommand;
use App\Module\Note\Domain\Interface\NoteWriterInterface;

readonly class DeleteNoteCommandHandler
{
    public function __construct(private NoteWriterInterface $noteWriterRepository,)
    {
    }

    public function __invoke(DeleteNoteCommand $command): void
    {
        $this->noteWriterRepository->deleteNoteInDB($command->getNote());
    }
}
