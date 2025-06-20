<?php

declare(strict_types=1);

namespace App\Module\Note\Presentation\API\Action;

use App\Module\Note\Application\Command\DeleteNoteCommand;
use App\Module\Note\Domain\Interface\NoteReaderInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class DeleteNoteAction
{
    public function __construct(private readonly MessageBusInterface $commandBus, private readonly NoteReaderInterface $noteReaderRepository)
    {
    }

    public function execute(string $uuid): void
    {
        $this->commandBus->dispatch(new DeleteNoteCommand($this->noteReaderRepository->getNoteByUUID($uuid)));
    }
}
