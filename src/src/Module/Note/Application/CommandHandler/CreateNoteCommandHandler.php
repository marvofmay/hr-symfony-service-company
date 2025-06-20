<?php

declare(strict_types=1);

namespace App\Module\Note\Application\CommandHandler;

use App\Module\Note\Application\Command\CreateNoteCommand;
use App\Module\Note\Domain\Service\NoteCreator;

readonly class CreateNoteCommandHandler
{
    public function __construct(private NoteCreator $noteCreator)
    {
    }

    public function __invoke(CreateNoteCommand $command): void
    {
        $this->noteCreator->create($command);
    }
}
