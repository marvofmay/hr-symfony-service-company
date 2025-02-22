<?php

declare(strict_types=1);

namespace App\Module\Note\Domain\Action;

use App\Module\Note\Application\Command\UpdateNoteCommand;
use App\Module\Note\Domain\DTO\UpdateDTO;
use App\Module\Note\Domain\Entity\Note;
use Symfony\Component\Messenger\MessageBusInterface;

class UpdateNoteAction
{
    public function __construct(private readonly MessageBusInterface $commandBus, private Note $note)
    {
    }

    public function setNoteToUpdate(Note $note): void
    {
        $this->note = $note;
    }

    public function getNote(): Note
    {
        return $this->note;
    }

    public function execute(UpdateDTO $updateDTO): void
    {
        $this->commandBus->dispatch(
            new UpdateNoteCommand(
                $updateDTO->getUUID(),
                $updateDTO->getTitle(),
                $updateDTO->getContent(),
                $updateDTO->getPriority(),
                $this->getNote()
            )
        );
    }
}
