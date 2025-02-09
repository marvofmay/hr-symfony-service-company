<?php

namespace App\Module\Note\Application\CommandHandler;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use App\Module\Note\Application\Command\DeleteNoteCommand;

readonly class DeleteNoteCommandHandler
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    public function __invoke(DeleteNoteCommand $command): void
    {
        $note = $command->getNote();
        $note->setDeletedAt(new DateTime());
        $this->entityManager->flush();
    }
}