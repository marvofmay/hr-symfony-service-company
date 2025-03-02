<?php

namespace App\Module\Note\Application\CommandHandler;

use App\Module\Note\Application\Command\DeleteNoteCommand;
use App\Module\Note\Domain\Service\NoteDeleter;

readonly class DeleteNoteCommandHandler
{
    public function __construct(private NoteDeleter $noteDeleter,)
    {
    }

    public function __invoke(DeleteNoteCommand $command): void
    {
        $this->noteDeleter->delete($command->getNote());
    }
}
