<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Position;

use App\Module\Company\Application\Command\Position\CreatePositionCommand;
use App\Module\Company\Domain\Entity\Position;
use App\Module\Company\Domain\Service\Position\PositionService;

readonly class CreatePositionCommandHandler
{
    public function __construct(private PositionService $positionService)
    {
    }

    public function __invoke(CreatePositionCommand $command): void
    {
        $position = new Position();
        $position->setName($command->name);
        $position->setDescription($command->description);
        $position->setActive($command->active);

        $this->positionService->savePositionInDB($position);
    }
}
