<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Position;

use App\Module\Company\Application\Command\Position\DeleteMultiplePositionsCommand;
use App\Module\Company\Domain\Interface\Position\PositionWriterInterface;

readonly class DeleteMultiplePositionsCommandHandler
{
    public function __construct( private PositionWriterInterface $positionWriterRepository)
    {
    }

    public function __invoke(DeleteMultiplePositionsCommand $command): void
    {
        $this->positionWriterRepository->deleteMultiplePositionsInDB($command->getPositions());
    }
}
