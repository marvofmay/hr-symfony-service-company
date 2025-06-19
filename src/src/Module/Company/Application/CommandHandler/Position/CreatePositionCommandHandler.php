<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Position;

use App\Module\Company\Application\Command\Position\CreatePositionCommand;
use App\Module\Company\Domain\Service\Position\PositionCreator;

readonly class CreatePositionCommandHandler
{
    public function __construct(private PositionCreator $positionCreator,) {}

    public function __invoke(CreatePositionCommand $command): void
    {
        $this->positionCreator->create($command);
    }
}
