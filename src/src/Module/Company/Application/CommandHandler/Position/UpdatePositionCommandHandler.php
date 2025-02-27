<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Position;

use App\Module\Company\Application\Command\Position\UpdatePositionCommand;
use App\Module\Company\Domain\Service\Position\PositionService;

readonly class UpdatePositionCommandHandler
{
    public function __construct(private PositionService $positionWriterService)
    {
    }

    public function __invoke(UpdatePositionCommand $command): void
    {
        $position = $command->getPosition();
        $position->setName($command->getName());
        $position->setDescription($command->getDescription());
        $position->setUpdatedAt(new \DateTime());

        $this->positionWriterService->updatePositionInDB($position);
    }
}
