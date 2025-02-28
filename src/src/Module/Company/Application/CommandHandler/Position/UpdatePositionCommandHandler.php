<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Position;

use App\Module\Company\Application\Command\Position\UpdatePositionCommand;
use App\Module\Company\Domain\Interface\Position\PositionWriterInterface;

readonly class UpdatePositionCommandHandler
{
    public function __construct(private PositionWriterInterface $positionWriterRepository,)
    {
    }

    public function __invoke(UpdatePositionCommand $command): void
    {
        $position = $command->getPosition();
        $position->setName($command->getName());
        $position->setDescription($command->getDescription());
        $position->setUpdatedAt(new \DateTime());

        $this->positionWriterRepository->updatePositionInDB($position);
    }
}
