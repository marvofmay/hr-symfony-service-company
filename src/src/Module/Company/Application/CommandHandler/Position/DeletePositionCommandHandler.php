<?php

namespace App\Module\Company\Application\CommandHandler\Position;

use App\Module\Company\Application\Command\Position\DeletePositionCommand;
use App\Module\Company\Domain\Service\Position\PositionDeleter;

readonly class DeletePositionCommandHandler
{
    public function __construct(private PositionDeleter $positionDeleter)
    {
    }

    public function __invoke(DeletePositionCommand $command): void
    {
        $this->positionDeleter->delete($command->getPosition());
    }
}
