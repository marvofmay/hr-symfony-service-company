<?php

namespace App\Module\Note\Application\CommandHandler;

use App\Module\Note\Application\Command\DeleteNoteCommand;
use Doctrine\ORM\EntityManagerInterface;

readonly class DeleteNoteCommandHandler
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function __invoke(DeleteNoteCommand $command): void
    {
        $note = $command->getNote();
        $note->setDeletedAt(new \DateTime());
        $this->entityManager->flush();
    }
}
