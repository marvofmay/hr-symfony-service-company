<?php

declare(strict_types=1);

namespace App\Module\Note\Application\CommandHandler;

use App\Module\Note\Application\Command\UpdateNoteCommand;
use App\Module\Note\Domain\Service\NoteUpdater;

readonly class UpdateNoteCommandHandler
{
    public function __construct(private NoteUpdater $noteUpdater)
    {
    }

    public function __invoke(UpdateNoteCommand $command): void
    {
        $this->noteUpdater->update($command->getNote(), $command->getTitle(), $command->getContent(), $command->getPriority());
    }
}
