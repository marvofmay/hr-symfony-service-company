<?php

namespace App\Module\Company\Application\CommandHandler\Position;

use App\Module\Company\Application\Command\Position\DeletePositionCommand;
use Doctrine\ORM\EntityManagerInterface;

readonly class DeletePositionCommandHandler
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function __invoke(DeletePositionCommand $command): void
    {
        $position = $command->getPosition();
        $this->entityManager->remove($position);
        $this->entityManager->flush();
    }
}
