<?php

namespace App\Module\Company\Application\CommandHandler\Position;

use App\Module\Company\Application\Command\Position\DeletePositionCommand;
use App\Module\Company\Domain\Interface\Position\PositionWriterInterface;

readonly class DeletePositionCommandHandler
{
    public function __construct(private PositionWriterInterface $positionWriterRepository)
    {
    }

    public function __invoke(DeletePositionCommand $command): void
    {
        $this->positionWriterRepository->deletePositionInDB($command->getPosition());
    }
}
