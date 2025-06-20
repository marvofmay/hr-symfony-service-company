<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Position;

use App\Module\Company\Application\Command\Position\UpdatePositionCommand;
use App\Module\Company\Domain\Service\Position\PositionUpdater;

readonly class UpdatePositionCommandHandler
{
    public function __construct(private PositionUpdater $positionUpdater)
    {
    }

    public function __invoke(UpdatePositionCommand $command): void
    {
        $this->positionUpdater->update($command);
    }
}
