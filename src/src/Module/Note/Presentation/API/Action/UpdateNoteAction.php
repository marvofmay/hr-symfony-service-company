<?php

declare(strict_types=1);

namespace App\Module\Note\Presentation\API\Action;

use App\Module\Note\Application\Command\UpdateNoteCommand;
use App\Module\Note\Domain\DTO\UpdateDTO;
use App\Module\Note\Domain\Interface\NoteReaderInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class UpdateNoteAction
{
    public function __construct(private readonly MessageBusInterface $commandBus, private readonly NoteReaderInterface $noteReaderRepository,)
    {
    }

    public function execute(UpdateDTO $updateDTO): void
    {
        $this->commandBus->dispatch(
            new UpdateNoteCommand(
                $updateDTO->getUUID(),
                $updateDTO->getTitle(),
                $updateDTO->getContent(),
                $updateDTO->getPriority(),
                $this->noteReaderRepository->getNoteByUUID($updateDTO->getUUID())
            )
        );
    }
}
