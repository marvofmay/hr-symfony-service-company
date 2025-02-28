<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Position;

use App\Module\Company\Application\Command\Position\DeleteMultiplePositionsCommand;
use App\Module\Company\Domain\Service\Position\PositionService;

readonly class DeleteMultiplePositionsCommandHandler
{
    public function __construct(private PositionService $roleService)
    {
    }

    public function __invoke(DeleteMultiplePositionsCommand $command): void
    {
        $this->roleService->deleteMultiplePositionsInDB($command->getPositions());
    }
}
