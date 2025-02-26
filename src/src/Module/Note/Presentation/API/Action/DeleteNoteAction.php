<?php

declare(strict_types=1);

namespace App\Module\Note\Presentation\API\Action;

use App\Module\Note\Application\Command\DeleteNoteCommand;
use App\Module\Note\Domain\Entity\Note;
use Symfony\Component\Messenger\MessageBusInterface;

class DeleteNoteAction
{
    public function __construct(private readonly MessageBusInterface $commandBus, private Note $note)
    {
    }

    public function setNoteToDelete(Note $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function execute(): void
    {
        $this->commandBus->dispatch(new DeleteNoteCommand($this->note));
    }
}
