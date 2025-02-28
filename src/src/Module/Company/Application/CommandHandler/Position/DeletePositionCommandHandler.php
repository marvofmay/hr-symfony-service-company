<?php

namespace App\Module\Company\Application\CommandHandler\Position;

use App\Module\Company\Application\Command\Position\DeletePositionCommand;
use App\Module\Company\Domain\Service\Position\PositionService;
use Doctrine\ORM\EntityManagerInterface;

readonly class DeletePositionCommandHandler
{
    public function __construct(private EntityManagerInterface $entityManager, private PositionService $positionService)
    {
    }

    public function __invoke(DeletePositionCommand $command): void
    {
        $this->positionService->deletePositionInDB($command->getPosition());
    }
}
